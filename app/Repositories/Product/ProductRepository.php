<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

/**
 * Class UserService
 * @package App\Services
 */
class ProductRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Product $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }

    public function search($keyword, $language_id)
    {
        return $this->model->select(
            [
                'products.id',
                'products.product_catalogue_id',
                'products.image',
                'products.icon',
                'products.album',
                'products.publish',
                'products.follow',
                'products.price',
                'products.stock',
                'products.code',
                'products.made_in',
                'products.attributeCatalogue',
                'products.attribute',
                'products.variant',
                'products.total_lesson',
                'products.duration',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
                // 'tb3.name as lecturer_name',
                // 'tb3.image as lecturer_image',
                // 'tb3.canonical as lecturer_canonical',
            ]
        )
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            // ->join('lecturers as tb3', 'tb3.id', '=','products.lecturer_id')
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) use ($language_id) {
                    $query->with(['attributes' => function ($query) use ($language_id) {
                        $query->with(['attribute_language' => function ($query) use ($language_id) {
                            $query->where('language_id', '=', $language_id);
                        }]);
                    }]);
                },
                'reviews',
            ])
            ->where('tb2.language_id', '=', $language_id)
            ->where('products.publish', '=', 2)
            ->where('tb2.name', 'LIKE', '%' . $keyword . '%')
            ->paginate(21)->withQueryString()->withPath(config('app.url') . 'tim-kiem');
    }

    public function findByIds($ids, $language_id)
    {
        return $this->model->select(
            [
                'products.id',
                'products.product_catalogue_id',
                'products.image',
                'products.icon',
                'products.album',
                'products.publish',
                'products.follow',
                'products.price',
                'products.stock',
                'products.code',
                'products.made_in',
                'products.attributeCatalogue',
                'products.attribute',
                'products.variant',
                'products.seller_id',
                'products.warranty',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) use ($language_id) {
                    $query->with(['attributes' => function ($query) use ($language_id) {
                        $query->with(['attribute_language' => function ($query) use ($language_id) {
                            $query->where('language_id', '=', $language_id);
                        }]);
                    }]);
                },
                'reviews'
            ])
            ->where('tb2.language_id', '=', $language_id)
            ->where('products.publish', '=', 2)
            ->whereIn('products.id', $ids)
            ->get();
    }


    public function searchForCompare(?string $keyword, int $language_id, int $limit = 15)
    {
        $query = $this->model->select(
            [
                'products.id',
                'products.image',
                'products.price',
                'products.code',
                'tb2.name',
                'tb2.canonical',
            ]
        )
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->where('tb2.language_id', '=', $language_id)
            ->where('products.publish', '=', 2)
            ->orderBy('products.id', 'desc')
            ->limit($limit);

        if (!empty($keyword)) {
            $query->where('tb2.name', 'LIKE', '%' . $keyword . '%');
        }

        return $query->get();
    }


    public function getProductById(int $id = 0, $language_id = 0, $condition = [])
    {
        return $this->model->select(
            [
                'products.id',
                'products.product_catalogue_id',
                'products.image',
                'products.icon',
                'products.album',
                'products.publish',
                'products.follow',
                'products.price',
                'products.stock',
                'products.code',
                'products.made_in',
                'products.attributeCatalogue',
                'products.attribute',
                'products.variant',
                'products.qrcode',
                'products.warranty',
                'products.iframe',
                'products.seller_id',
                // 'products.total_lesson',
                // 'products.duration',
                // 'products.lecturer_id',
                // 'products.chapter',
                // 'products.lession_content',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) use ($language_id) {
                    $query->with(['attributes' => function ($query) use ($language_id) {
                        $query->with(['attribute_language' => function ($query) use ($language_id) {
                            $query->where('language_id', '=', $language_id);
                        }]);
                    }]);
                },
                'reviews' => function ($query) {
                    $query->where('status', '=', 1);
                },
            ])
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }

    public function findProductForPromotion($condition = [], $relation = [])
    {
        $query = $this->model->newQuery();
        $query->select([
            'products.id',
            'products.image',
            'products.warranty',
            'tb2.name',
            'tb3.uuid',
            'tb3.id as product_variant_id',
            DB::raw('CONCAT(tb2.name, " - ", COALESCE(tb4.name, " Default")) as variant_name'),
            DB::raw('COALESCE(tb3.sku, products.code) as sku'),
            DB::raw('COALESCE(tb3.price, products.price) as price'),
        ]);
        $query->join('product_language as tb2', 'products.id', '=', 'tb2.product_id');
        $query->leftJoin('product_variants as tb3', 'products.id', '=', 'tb3.product_id');
        $query->leftJoin('product_variant_language as tb4', 'tb3.id', '=', 'tb4.product_variant_id');

        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        if (count($relation)) {
            $query->with($relation);
        }
        $query->orderBy('id', 'desc');
        $query->groupBy('products.id');
        return $query->paginate(20);
    }

    public function filter($param, $perpage, $orderBy)
    {
        $query = $this->model->newQuery();

        $query->select(
            'products.id',
            'products.price',
            'products.image',
            // 'products.lecturer_id',
            'product_language.name as name'
        );

        if (isset($param['select']) && count($param['select'])) {
            foreach ($param['select'] as $key => $val) {
                if (is_null($val)) continue;
                $query->selectRaw($val);
            }
        }

        if (isset($param['join']) && count($param['join'])) {
            foreach ($param['join'] as $key => $val) {
                if (is_null($val)) continue;
                $query->leftJoin($val[0], $val[1], $val[2], $val[3]);
            }
        }

        $query->where('products.publish', '=', 2);

        if (isset($param['where']) && count($param['where'])) {
            foreach ($param['where'] as $key => $val) {
                $query->where($val);
            }
        }

        if (!empty($param['whereRaw']) && !is_null($param['whereRaw'][0])) {
            $query->where(function ($q) use ($param) {
                foreach ($param['whereRaw'] as $raw) {
                    $q->orWhereRaw($raw[0], $raw[1]);
                }
            });
        }

        if (isset($param['whereIn']) && count($param['whereIn'])) {
            foreach ($param['whereIn'] as $key => $val) {
                $query->whereIn($val['field'], $val['value']);
            }
        }

        if (isset($param['having']) && count($param['having'])) {
            foreach ($param['having'] as $key => $val) {
                if (is_null($val)) continue;
                $query->having($val);
            }
        }
        [$column, $direction] = $orderBy;
        $query->orderBy($column, $direction);
        $query->groupBy('products.id');
        $query->with(['reviews', 'languages', 'product_catalogues', 'lecturers']);
        return $query->paginate($perpage);
    }


    public function findProductForVoucher($condition = [], $relation = [])
    {
        $query = $this->model->newQuery();
        $query->select([
            'products.id',
            'products.code',
            'products.price',
            'products.image',
            'tb2.name',
        ]);
        $query->join('product_language as tb2', 'products.id', '=', 'tb2.product_id');

        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        if (count($relation)) {
            $query->with($relation);
        }
        $query->orderBy('id', 'desc');
        $query->groupBy('products.id');
        return $query->paginate(20);
    }

    public function getRelated($limit = 6, $productCatalogueId = 0, $productId = 0)
    {
        return $this->model->where('publish', 2)->where('product_catalogue_id', $productCatalogueId)->where('id', '!=', $productId)->orderBy('id', 'desc')->limit($limit)->get();
    }

    public function getProductByProductCatalogue(array $productCatalogue = [], $language_id = 0)
    {
        $catalogueIds = implode(',', $productCatalogue);
        $catalogueCount = count($productCatalogue);
        $products = Product::withCount(['reviews as review_count'])
            ->withAvg('reviews as review_average', 'score')
            ->select([
                'products.id',
                'products.price',
                'products.image',
                'products.total_lesson',
                'products.duration',
                'tb2.name',
                'tb2.canonical',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb3.name as lecturer_name',
                'tb3.image as lecturer_avatar',
                'tb3.canonical as lecturer_canonical'
            ])
            ->whereRaw("products.id IN (
                SELECT product_id
                FROM product_catalogue_product
                WHERE product_catalogue_id IN ({$catalogueIds})
                GROUP BY product_id
                HAVING COUNT(DISTINCT product_catalogue_id) = {$catalogueCount}
            )")
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->leftJoin('lecturers as tb3', 'tb3.id', '=', 'products.lecturer_id')
            ->where('tb2.language_id', '=', $language_id)
            ->get();

        return $products;
    }
}
