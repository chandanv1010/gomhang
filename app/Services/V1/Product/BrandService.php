<?php

namespace App\Services\V1\Product;

use Illuminate\Support\Facades\DB;

/**
 * Thương hiệu sản phẩm.
 *
 * Thương hiệu là các thuộc tính trong nhóm "Thương hiệu"
 * (config apps.general.brandAttributeCatalogueId). Sản phẩm gắn thương hiệu qua
 * cột products.brand_id. Trước đây chỉ gắn được qua khối "phiên bản" (lưu trong
 * products.attribute), cách đó sinh phiên bản theo thương hiệu nên không ai dùng;
 * vẫn đọc cả products.attribute để không bỏ sót dữ liệu cũ.
 *
 * Dữ liệu có thương hiệu trùng tên/đường dẫn (bản mẫu ban đầu và bản admin tạo
 * lại, ví dụ hai "Xiaomi" cùng canonical "xiaomi"), nên mọi chỗ lọc đều coi các
 * thuộc tính cùng canonical là một thương hiệu.
 */
class BrandService
{
    public function catalogueId(): int
    {
        return (int) config('apps.general.brandAttributeCatalogueId');
    }

    /**
     * Danh sách cho ô chọn trong form sản phẩm: mỗi thương hiệu một dòng, ưu tiên
     * bản có trang riêng (có đường dẫn trong bảng routers).
     *
     * @return array<int, string> id => tên
     */
    public function options(int $languageId = 1): array
    {
        $rows = DB::table('attributes as a')
            ->join('attribute_language as al', 'al.attribute_id', '=', 'a.id')
            ->where('al.language_id', $languageId)
            ->where('a.attribute_catalogue_id', $this->catalogueId())
            ->where('a.publish', 2)
            ->whereNull('a.deleted_at')
            ->select('a.id', 'al.name', 'al.canonical')
            ->selectRaw(
                'EXISTS(SELECT 1 FROM routers r WHERE r.module_id = a.id AND r.controllers = ?) as has_page',
                [\App\Http\Controllers\Frontend\AttributeController::class]
            )
            ->orderByDesc('has_page')
            ->orderBy('a.id')
            ->get();

        $options = [];
        $seen = [];
        foreach ($rows as $row) {
            $key = $row->canonical ?: mb_strtolower($row->name);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $options[$row->id] = $row->name;
        }
        asort($options, SORT_NATURAL | SORT_FLAG_CASE);
        return $options;
    }

    /**
     * Tất cả id thuộc tính coi là cùng một thương hiệu với $brand (id hoặc
     * canonical).
     *
     * @return int[]
     */
    public function sameBrandIds($brand): array
    {
        $query = DB::table('attributes as a')
            ->join('attribute_language as al', 'al.attribute_id', '=', 'a.id')
            ->where('a.attribute_catalogue_id', $this->catalogueId())
            ->whereNull('a.deleted_at');

        if (is_numeric($brand)) {
            $canonical = DB::table('attribute_language')
                ->where('attribute_id', (int) $brand)
                ->value('canonical');
            if (empty($canonical)) {
                return [(int) $brand];
            }
            $query->where('al.canonical', $canonical);
        } else {
            $query->where(function ($q) use ($brand) {
                $q->where('al.canonical', $brand)->orWhere('al.name', $brand);
            });
        }

        return $query->distinct()->pluck('a.id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * Điều kiện SQL "sản phẩm thuộc một trong các thương hiệu $ids".
     *
     * @return array{0: string, 1: array}
     */
    public function productCondition(array $ids, string $alias = 'products'): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!count($ids)) {
            return ['0 = 1', []];
        }

        $path = '$."' . $this->catalogueId() . '"';
        $parts = [$alias . '.brand_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')'];
        $bindings = $ids;
        foreach ($ids as $id) {
            // Mã trong JSON cũ có thể là chuỗi "25" hoặc số 25. Bind dạng chuỗi để
            // PDO gửi text (bind int thì MySQL báo lỗi 3146).
            $parts[] = "JSON_CONTAINS({$alias}.attribute, ?, '{$path}')";
            $parts[] = "JSON_CONTAINS({$alias}.attribute, ?, '{$path}')";
            $bindings[] = '"' . $id . '"';
            $bindings[] = (string) $id;
        }

        return ['(' . implode(' OR ', $parts) . ')', $bindings];
    }

    /** Tên thương hiệu của sản phẩm, rỗng nếu chưa gắn. */
    public function nameOf($product, int $languageId = 1): string
    {
        $id = (int) ($product->brand_id ?? 0);
        if (!$id) {
            $legacy = $product->attribute[$this->catalogueId()] ?? [];
            $id = (int) (is_array($legacy) ? reset($legacy) : 0);
        }
        if (!$id) {
            return '';
        }

        return (string) DB::table('attribute_language')
            ->where('attribute_id', $id)
            ->where('language_id', $languageId)
            ->value('name');
    }
}
