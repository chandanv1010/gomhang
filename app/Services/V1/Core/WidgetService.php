<?php

namespace App\Services\V1\Core;

use App\Services\V1\BaseService;

use App\Repositories\Core\WidgetRepository;
use App\Repositories\Product\PromotionRepository;
use App\Repositories\Product\ProductCatalogueRepository;
use App\Services\V1\Product\ProductService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class WidgetService - Fixed Pure Query Builder Implementation
 * @package App\Services
 * Fix Widget mới
 */
class WidgetService extends BaseService
{
    protected $widgetRepository;
    protected $promotionRepository;
    protected $productRepository;
    protected $productCatalogueRepository;
    protected $productService;

    protected static $widgetCache = [];

    public function __construct(
        WidgetRepository $widgetRepository,
        PromotionRepository $promotionRepository,
        ProductCatalogueRepository $productCatalogueRepository,
        ProductService $productService,
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->promotionRepository = $promotionRepository;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productService = $productService;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = $request->integer('perpage');
        $widgets = $this->widgetRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'widget/index'],
        );

        return $widgets;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'album', 'model', 'note');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            $widget = $this->widgetRepository->create($payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($id, $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'album', 'model', 'note');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];

            $widget = $this->widgetRepository->update($id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $widget = $this->widgetRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function saveTranslate($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $temp = [];
            $translateId = $request->input('translateId');
            $widget = $this->widgetRepository->findById($request->input('widgetId'));
            $temp = $widget->description;
            $temp[$translateId] = $request->input('translate_description');
            $payload['description'] = $temp;
            $this->widgetRepository->update($widget->id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function paginateSelect()
    {
        return [
            'id',
            'name',
            'keyword',
            'short_code',
            'publish',
            'description',
            'note'
        ];
    }

    /* FRONTEND SERVICE */

    /**
     * REBUILT getWidget() - Pure Query Builder approach
     */
    public function getWidget(array $params = [], int $language = 1): array
    {

        $cacheKey = md5(serialize($params) . '_lang_' . $language);

        if (isset(static::$widgetCache[$cacheKey])) {
            return static::$widgetCache[$cacheKey];
        }

        $keywords = array_column($params, 'keyword');

        $paramsMap = collect($params)->keyBy('keyword');

        // SINGLE QUERY 1: Get all widgets
        $widgets = $this->getWidgetsQuery($keywords);

        if ($widgets->isEmpty()) {
            return static::$widgetCache[$cacheKey] = [];
        }

        $widgetsByModel = $widgets->groupBy('model');

        $result = [];

        foreach ($widgetsByModel as $model => $modelWidgets) {
            $modelResult = $this->processWidgetsByModel($model, $modelWidgets, $paramsMap, $language);
            $result = array_merge($result, $modelResult);
        }

        return static::$widgetCache[$cacheKey] = $result;
    }

    /**
     * Get widgets với single query - FIXED return type
     */
    private function getWidgetsQuery(array $keywords): Collection
    {
        if (empty($keywords)) {
            return collect();
        }

        $escapedKeywords = array_map('addslashes', $keywords);

        $keywordList = "'" . implode("','", $escapedKeywords) . "'";

        $results = DB::select("
            SELECT id, name, keyword, model, model_id, short_code, description, album 
            FROM widgets 
            WHERE keyword IN ({$keywordList})
            AND publish = 2 
            AND deleted_at IS NULL
            ORDER BY FIELD(keyword, {$keywordList})
        ");

        return collect($results)->map(function ($widget) {
            $widget->description = json_decode($widget->description ?? '{}', true);
            $widget->album = json_decode($widget->album ?? '[]', true);
            // Decode model_id if it's a JSON string
            $widget->model_id = is_string($widget->model_id) && json_decode($widget->model_id, true)
                ? json_decode($widget->model_id, true)
                : [$widget->model_id];
            return $widget;
        });
    }

    /**
     * Process widgets by model type
     */
    private function processWidgetsByModel(string $model, Collection $widgets, Collection $paramsMap, int $language): array
    {
        $isCatalogue = strpos($model, 'Catalogue') !== false;

        if ($isCatalogue) {
            return $this->processCatalogueWidgets($model, $widgets, $paramsMap, $language);
        } else {
            return $this->processRegularWidgets($model, $widgets, $paramsMap, $language);
        }
    }

    /**
     * Process Catalogue widgets (ProductCatalogue, PostCatalogue, etc.)
     */
    private function processCatalogueWidgets(string $model, Collection $widgets, Collection $paramsMap, int $language): array
    {

        $tableName = $this->getTableName($model);

        // Flatten model_id arrays
        $modelIds = $widgets->pluck('model_id')
            ->flatten()
            ->filter() // Loại bỏ giá trị null hoặc rỗng
            ->map(function ($id) {
                return is_numeric($id) ? (int) $id : 0; // Chuyển đổi thành số nguyên, ghi log nếu có lỗi
            })
            ->filter(function ($id) {
                return $id > 0; // Loại bỏ các giá trị 0
            })
            ->unique()
            ->values() // Đảm bảo chỉ số mảng liên tục
            ->toArray();

        if (empty($modelIds)) {
            return [];
        }


        // SINGLE QUERY 2: Get all catalogue data with languages
        $catalogues = $this->getCataloguesWithLanguages($tableName, $modelIds, $language);

        // SINGLE QUERY 3: Get children if needed (batch)
        $childrenMap = $this->getChildrenBatch($tableName, $modelIds, $language, $widgets, $paramsMap);

        // SINGLE QUERY 4: Get all products/posts for all categories (batch)
        $objectsMap = $this->getObjectsBatch($model, $catalogues, $language, $widgets, $paramsMap);

        $result = [];

        foreach ($widgets as $widget) {
            // Handle multiple model_ids
            $widgetModelIds = is_array($widget->model_id) ? $widget->model_id : [$widget->model_id];

            $cataloguesForWidget = $catalogues->whereIn('id', $widgetModelIds);

            if ($cataloguesForWidget->isEmpty()) {
                continue;
            }

            foreach ($cataloguesForWidget as $catalogue) {
                // Attach children if needed
                if (isset($childrenMap[$catalogue->id])) {
                    $catalogue->childrens = $childrenMap[$catalogue->id];
                }

                // Attach objects (products/posts)
                $objectKey = $this->getObjectKey($model);
                $catalogue->{$objectKey} = $objectsMap[$catalogue->id] ?? collect();

                // Handle promotions if needed
                $params = $paramsMap->get($widget->keyword, []);
                if (isset($params['promotion']) && $params['promotion'] && $catalogue->{$objectKey}->isNotEmpty()) {
                    $catalogue->{$objectKey} = $this->applyPromotions($catalogue->{$objectKey}, $model);
                }

                $widget->object = $widget->object ?? collect();
                $widget->object->push($catalogue);
            }

            $result[$widget->keyword] = $widget;
        }
        return $result;
    }

    /**
     * Process Regular widgets (Product, Post, etc.)
     */
    private function processRegularWidgets(string $model, Collection $widgets, Collection $paramsMap, int $language): array
    {
        $tableName = $this->getTableName($model);
        // Flatten model_id arrays
        $modelIds = $widgets->pluck('model_id')
            ->flatten()
            ->filter() // Loại bỏ giá trị null hoặc rỗng
            ->map(function ($id) {
                return is_numeric($id) ? (int) $id : 0; // Chuyển đổi thành số nguyên, ghi log nếu có lỗi
            })
            ->filter(function ($id) {
                return $id > 0; // Loại bỏ các giá trị 0
            })
            ->unique()
            ->values() // Đảm bảo chỉ số mảng liên tục
            ->toArray();


        if (empty($modelIds)) {
            return [];
        }

        // Create modelIdList for the query
        $modelIdList = implode(',', $modelIds);

        // SINGLE QUERY: Get all objects with relationships
        $objects = $this->getObjectsWithRelationships($tableName, $modelIds, $model, $language);

        $result = [];
        foreach ($widgets as $widget) {
            // Handle multiple model_ids
            $widgetModelIds = is_array($widget->model_id) ? $widget->model_id : [$widget->model_id];
            $objectsForWidget = $objects->whereIn('id', $widgetModelIds);

            if ($objectsForWidget->isEmpty()) {
                continue;
            }

            foreach ($objectsForWidget as $object) {
                // Handle promotions if needed
                $params = $paramsMap->get($widget->keyword, []);
                if (isset($params['promotion']) && $params['promotion']) {
                    $promotedObjects = $this->applyPromotions(collect([$object]), $model);
                    $object = $promotedObjects->first();
                }

                $widget->object = $widget->object ?? collect();
                $widget->object->push($object);
            }

            $result[$widget->keyword] = $widget;
        }

        return $result;
    }

    /**
     * Get catalogues with languages in single query
     */
    private function getCataloguesWithLanguages(string $tableName, array $modelIds, int $language): Collection
    {
        if (empty($modelIds)) {
            return collect();
        }

        $languageTable = str_replace('_catalogues', '_catalogue_language', $tableName);
        $catalogueIdField = $this->getCatalogueIdField($tableName);
        $modelIdList = implode(',', array_map('intval', $modelIds));

        $results = DB::select("
            SELECT 
                c.*,
                cl.name as language_name,
                cl.canonical,
                cl.meta_title,
                cl.meta_description,
                cl.description as language_description,
                cl.content
            FROM {$tableName} c
            LEFT JOIN {$languageTable} cl ON c.id = cl.{$catalogueIdField} 
                AND cl.language_id = ?
            WHERE c.id IN ({$modelIdList})
            AND c.publish = 2 
            AND c.deleted_at IS NULL
            ORDER BY c.order DESC, c.id DESC
        ", [$language]);

        return collect($results)->map(function ($item) {
            // Convert to object with proper structure
            $item->languages = (object) [
                'name' => $item->language_name,
                'canonical' => $item->canonical,
                'meta_title' => $item->meta_title,
                'meta_description' => $item->meta_description,
                'description' => $item->language_description,
                'content' => $item->content
            ];
            unset(
                $item->language_name,
                $item->canonical,
                $item->meta_title,
                $item->meta_description,
                $item->language_description,
                $item->content
            );
            return $item;
        });
    }

    /**
     * Get children batch
     */
    private function getChildrenBatch(string $tableName, array $parentIds, int $language, Collection $widgets, Collection $paramsMap): array
    {
        $needsChildren = [];
        foreach ($widgets as $widget) {
            $params = $paramsMap->get($widget->keyword, []);
            if (isset($params['children']) && $params['children']) {
                $widgetModelIds = is_array($widget->model_id) ? $widget->model_id : [$widget->model_id];
                $needsChildren = array_merge($needsChildren, $widgetModelIds);
            }
        }

        if (empty($needsChildren)) {
            return [];
        }

        $languageTable = str_replace('_catalogues', '_catalogue_language', $tableName);
        $catalogueIdField = $this->getCatalogueIdField($tableName);
        $parentIdList = implode(',', array_map('intval', $needsChildren));

        $results = DB::select("
            SELECT 
                c.*,
                c.parent_id,
                cl.name as language_name,
                cl.canonical,
                cl.meta_title,
                cl.meta_description
            FROM {$tableName} c
            LEFT JOIN {$languageTable} cl ON c.id = cl.{$catalogueIdField} 
                AND cl.language_id = ?
            WHERE c.parent_id IN ({$parentIdList})
            AND c.publish = 2 
            AND c.deleted_at IS NULL
            ORDER BY c.order ASC
        ", [$language]);

        $children = collect($results)->map(function ($item) {
            $item->languages = (object) [
                'name' => $item->language_name,
                'canonical' => $item->canonical,
                'meta_title' => $item->meta_title,
                'meta_description' => $item->meta_description
            ];
            unset($item->language_name, $item->canonical, $item->meta_title, $item->meta_description);
            return $item;
        });

        return $children->groupBy('parent_id')->toArray();
    }

    private function normalizeField(string $catalogueModel): string
    {
        return Str::snake(str_replace('Catalogue', '_catalogue', $catalogueModel)) . '_id';
    }

    private function getObjectsBatch(string $catalogueModel, Collection $catalogues, int $language, Collection $widgets, Collection $paramsMap): array
    {
        if ($catalogues->isEmpty()) {
            return [];
        }

        $objectModel = $this->getObjectModel($catalogueModel);
        $objectTable = $this->getTableName($objectModel);
        $pivotTable = $this->getPivotTableName($objectModel);
        $catalogueIdField = $this->normalizeField($catalogueModel);
        $objectIdField = $objectModel . '_id';

        // Lấy tất cả category IDs (bao gồm con cháu)
        $allCategoryIds = [];
        foreach ($catalogues as $catalogue) {
            $recursiveIds = $this->getRecursiveCategoryIds($catalogue->id, $catalogueModel);
            $allCategoryIds = array_merge($allCategoryIds, $recursiveIds);
        }
        $allCategoryIds = array_unique(array_filter($allCategoryIds));

        if (empty($allCategoryIds)) {
            return [];
        }

        $objectLanguageTable = $objectModel . '_language';
        $objectCatalogueTable = $this->getTableName($catalogueModel);
        $objectCatalogueLanguageTable = str_replace('_catalogues', '_catalogue_language', $objectCatalogueTable);
        $categoryIdList = implode(',', array_map('intval', $allCategoryIds));

        // --- FULL SQL ---
        $sql = "
            SELECT 
                o.*,
                p.{$catalogueIdField} as category_id,
                ol.name as language_name,
                ol.canonical,
                ol.meta_title,
                ol.meta_description,
                ol.description as language_description,
                ol.content,
                GROUP_CONCAT(DISTINCT oc.id) as catalogue_ids,
                GROUP_CONCAT(DISTINCT ocl.name) as catalogue_names
        ";

        if ($objectModel === 'product') {
            $sql .= ",
             
                COUNT(DISTINCT r.id) as review_count,
                COALESCE(AVG(r.score), 0) as review_average,
                GROUP_CONCAT(DISTINCT pv.id) as variant_ids
            ";
        }

        $sql .= "
            FROM {$objectTable} o
            INNER JOIN {$pivotTable} p ON o.id = p.{$objectIdField}
            LEFT JOIN {$objectLanguageTable} ol ON o.id = ol.{$objectIdField} AND ol.language_id = ?
            LEFT JOIN {$pivotTable} poc ON o.id = poc.{$objectIdField}
            LEFT JOIN {$objectCatalogueTable} oc ON poc.{$catalogueIdField} = oc.id
            LEFT JOIN {$objectCatalogueLanguageTable} ocl ON oc.id = ocl.{$this->getCatalogueIdField($objectCatalogueTable)} 
                AND ocl.language_id = ?
        ";

        if ($objectModel === 'product') {
            $sql .= "
              
                LEFT JOIN reviews r ON o.id = r.reviewable_id AND r.reviewable_type = 'App\\\\Models\\\\Product'
                LEFT JOIN product_variants pv ON o.id = pv.product_id
            ";
        }

        $sql .= "
            WHERE p.{$catalogueIdField} IN ({$categoryIdList})
            AND o.publish = 2 
            AND o.deleted_at IS NULL
            GROUP BY o.id, p.{$catalogueIdField}
            ORDER BY o.order DESC, o.id DESC
        ";

        // Query DB
        $rows = collect(DB::select($sql, [$language, $language]));

        // Gom objects theo category_id
        $objectsByCategory = [];
        foreach ($rows as $row) {
            $cid = (int)($row->category_id ?? 0);
            if ($cid <= 0) continue;

            if ($objectModel === 'product') {
                $row->image = image($row->image);
            }

            // Chuẩn hóa languages
            $languageData = (object) [
                'name'             => $row->language_name,
                'canonical'        => $row->canonical,
                'meta_title'       => $row->meta_title,
                'meta_description' => $row->meta_description,
                'description'      => $row->language_description,
                'content'          => $row->content,
                'pivot'            => (object)[
                    'name'      => $row->language_name,
                    'canonical' => $row->canonical,
                ]
            ];
            $row->languages = collect([$languageData]);

            // Chuẩn hóa catalogue quan hệ
            if ($row->catalogue_ids) {
                $catalogueIds = explode(',', $row->catalogue_ids);
                $catalogueNames = explode(',', $row->catalogue_names);
                $row->product_catalogues = collect($catalogueIds)->map(function ($id, $idx) use ($catalogueNames) {
                    return (object)[
                        'id'        => $id,
                        'languages' => (object)['name' => $catalogueNames[$idx] ?? '']
                    ];
                });
            }

            // Product-specific
            if ($objectModel === 'product') {
                $row->review_count   = (int) $row->review_count;
                $row->review_average = round((float) $row->review_average, 1);

                if ($row->variant_ids) {
                    $row->product_variants = collect(explode(',', $row->variant_ids))->map(function ($id) {
                        return (object)['id' => $id];
                    });
                }
            }

            unset(
                $row->language_name,
                $row->canonical,
                $row->meta_title,
                $row->meta_description,
                $row->language_description,
                $row->content,
                $row->catalogue_ids,
                $row->catalogue_names,
                $row->variant_ids,
                $row->category_id
            );

            if (!isset($objectsByCategory[$cid])) {
                $objectsByCategory[$cid] = collect();
            }
            $objectsByCategory[$cid]->push($row);
        }

        // Map lại theo từng catalogue
        $result = [];
        foreach ($catalogues as $catalogue) {
            $recursiveIds = $this->getRecursiveCategoryIds($catalogue->id, $catalogueModel);

            // merge objects thuộc tất cả category con
            $merged = collect();
            foreach ($recursiveIds as $cid) {
                if (isset($objectsByCategory[$cid])) {
                    $merged = $merged->merge($objectsByCategory[$cid]);
                }
            }

            $merged = $merged->unique('id');

            // Apply limit từ widget
            $widget = $widgets->firstWhere('model_id', function ($modelId) use ($catalogue) {
                return is_array($modelId) ? in_array($catalogue->id, $modelId) : $modelId == $catalogue->id;
            });
            if ($widget) {
                $params = $paramsMap->get($widget->keyword, []);
                $limit  = $params['limit'] ?? 10;
                $merged = $merged->take($limit);
            }

            $result[$catalogue->id] = $merged->values();
        }

        return $result;
    }

    

    /**
     * Get objects with relationships for regular widgets
     */
    private function getObjectsWithRelationships(string $tableName, array $modelIds, string $model, int $language): Collection
    {
        if (empty($modelIds)) {
            return collect();
        }

        $languageTable = Str::snake($model) . '_language';
        $catalogueTable = Str::snake($model) . '_catalogues';
        $catalogueLanguageTable = Str::snake($model) . '_catalogue_language';
        $pivotTable = Str::snake($model) . '_catalogue_' . Str::snake($model);
        $modelIdList = implode(',', array_map('intval', $modelIds));


        $sql = "
            SELECT 
                o.*,
                ol.name as language_name,
                ol.canonical,
                ol.meta_title,
                ol.meta_description,
                ol.description as language_description,
                ol.content,
                GROUP_CONCAT(DISTINCT c.id) as catalogue_ids,
                GROUP_CONCAT(DISTINCT cl.name) as catalogue_names
        ";

        if ($model === 'Product') {
            $sql .= ",
                COUNT(DISTINCT r.id) as review_count,
                COALESCE(AVG(r.score), 0) as review_average
            ";
        }

        $sql .= "
            FROM {$tableName} o
            LEFT JOIN {$languageTable} ol ON o.id = ol.{$model}_id AND ol.language_id = ?
            LEFT JOIN {$pivotTable} pc ON o.id = pc.{$model}_id
            LEFT JOIN {$catalogueTable} c ON pc.{$model}_catalogue_id = c.id
            LEFT JOIN {$catalogueLanguageTable} cl ON c.id = cl.{$model}_catalogue_id AND cl.language_id = ?
        ";


        if ($model === 'Product') {
            $sql .= "
                LEFT JOIN reviews r ON o.id = r.reviewable_id AND r.reviewable_type = 'App\\\\Models\\\\Product'
            ";
        }

        $sql .= "
            WHERE o.id IN ({$modelIdList})
            AND o.publish = 2 
            AND o.deleted_at IS NULL
            GROUP BY o.id
            ORDER BY o.order DESC
        ";

        return collect(DB::select($sql, [$language, $language]))->map(function ($item) use ($model) {
            // Process similar to getObjectsBatch
            $item->languages = (object) [
                'name' => $item->language_name,
                'canonical' => $item->canonical,
                'meta_title' => $item->meta_title,
                'meta_description' => $item->meta_description,
                'description' => $item->language_description,
                'content' => $item->content
            ];

            if ($item->catalogue_ids) {
                $catalogueIds = explode(',', $item->catalogue_ids);
                $catalogueNames = explode(',', $item->catalogue_names);
                $relationName = strtolower($model) . '_catalogues';
                $item->{$relationName} = collect($catalogueIds)->map(function ($id, $index) use ($catalogueNames) {
                    return (object) [
                        'id' => $id,
                        'languages' => (object) ['name' => $catalogueNames[$index] ?? '']
                    ];
                });
            }

            if ($model === 'Product') {
                $item->image = image($item->image);
                $item->review_count = (int) $item->review_count;
                $item->review_average = round((float) $item->review_average, 1);
            }

            unset(
                $item->language_name,
                $item->canonical,
                $item->meta_title,
                $item->meta_description,
                $item->language_description,
                $item->content,
                $item->catalogue_ids,
                $item->catalogue_names
            );

            return $item;
        });
    }

    /**
     * Get recursive category IDs - Fallback version for older MySQL
     */
    private function getRecursiveCategoryIds(int $categoryId, string $catalogueModel): array
    {
        static $recursiveCache = [];
        $cacheKey = $catalogueModel . '_' . $categoryId;

        if (isset($recursiveCache[$cacheKey])) {
            return $recursiveCache[$cacheKey];
        }

        $tableName = $this->getTableName($catalogueModel);

        // Fallback: Simple recursive approach (compatible with older MySQL)
        $ids = [$categoryId];
        $this->collectChildrenIds($tableName, $categoryId, $ids);

        return $recursiveCache[$cacheKey] = array_unique($ids);
    }

    /**
     * Recursive helper to collect all children IDs
     */
    private function collectChildrenIds(string $tableName, int $parentId, array &$ids): void
    {
        $children = DB::select("
            SELECT id FROM {$tableName} 
            WHERE parent_id = ? AND deleted_at IS NULL
        ", [$parentId]);

        foreach ($children as $child) {
            if (!in_array($child->id, $ids)) {
                $ids[] = $child->id;
                $this->collectChildrenIds($tableName, $child->id, $ids);
            }
        }
    }

    /**
     * Apply promotions (simplified - integrate with existing promotion service)
     */
    private function applyPromotions(Collection $objects, string $model): Collection
    {
        if (!in_array($model, ['Product', 'ProductCatalogue']) && !$objects->isNotEmpty()) {
            return $objects;
        }
        $productIds = $objects->pluck('id')->toArray();
        if(!empty($productIds) && is_array($productIds)){
            $promotions = $this->promotionRepository->findByProduct($productIds);
            if($promotions->isNotEmpty()){
                $promotionMap = $promotions->keyBy('product_id');
                foreach($objects as $index => $product){
                    if($promotionMap->has($product->id)){
                        $objects[$index]->promotions = $promotionMap->get($product->id);
                    }
                }
            }
        }
        return $objects;
    }

    // Helper methods
    private function getTableName(string $model): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model)) . 's';
    }

    private function getObjectModel(string $catalogueModel): string
    {
        return lcfirst(str_replace('Catalogue', '', $catalogueModel));
    }

    private function getObjectKey(string $catalogueModel): string
    {
        return $this->getObjectModel($catalogueModel) . 's';
    }

    private function getPivotTableName(string $objectModel): string
    {
        return $objectModel . '_catalogue_' . $objectModel;
    }

    private function getCatalogueIdField(string $tableName): string
    {
        $model = rtrim($tableName, 's');
        return $model . '_id';
    }

    private function getCatalogueIdFieldForPivot(string $catalogueModel): string
    {
        return strtolower($catalogueModel) . '_id';
    }
}