<?php

namespace App\Services\V1\Core;

use App\Services\V1\Product\BrandService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Sinh public/sitemap.xml từ dữ liệu thật của site.
 *
 * File sitemap.xml trước đây là file tĩnh tạo bằng công cụ online cho một
 * website khác (noithatanhung.vn), không có URL nào của site này.
 *
 * Vẫn ghi ra file tĩnh (thay vì route động) vì nginx phục vụ thẳng file trong
 * public/ và không chắc request *.xml được chuyển vào Laravel. File được ghi lại
 * mỗi khi sản phẩm, danh mục, bài viết được lưu/xoá (xem AppServiceProvider)
 * và có thể chạy tay: php artisan sitemap:generate
 */
class SitemapService
{
    // Frontend đang cố định ngôn ngữ 1 (FrontendController::$language).
    private const LANGUAGE_ID = 1;

    // Controller trong bảng routers => bảng dữ liệu tương ứng, độ ưu tiên.
    private const MODULES = [
        'App\Http\Controllers\Frontend\ProductCatalogueController' => ['product_catalogues', '0.8', 'daily'],
        'App\Http\Controllers\Frontend\ProductController' => ['products', '0.8', 'weekly'],
        'App\Http\Controllers\Frontend\AttributeController' => ['attributes', '0.7', 'weekly'],
        'App\Http\Controllers\Frontend\PostCatalogueController' => ['post_catalogues', '0.6', 'weekly'],
        'App\Http\Controllers\Frontend\PostController' => ['posts', '0.6', 'monthly'],
    ];

    // Các trang có route riêng (không nằm trong bảng routers).
    private const STATIC_PAGES = ['gioi-thieu', 'lien-he', 'cong-nghe', 'phan-phoi'];

    public function urls(): array
    {
        $urls = [[
            'loc' => rtrim(config('app.url'), '/') . '/',
            'lastmod' => null,
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]];

        foreach (self::STATIC_PAGES as $canonical) {
            $urls[] = [
                'loc' => write_url($canonical),
                'lastmod' => null,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        }

        foreach (self::MODULES as $controller => [$table, $priority, $changefreq]) {
            $query = DB::table('routers as r')
                ->join($table . ' as m', 'm.id', '=', 'r.module_id')
                ->where('r.controllers', $controller)
                ->where('r.language_id', self::LANGUAGE_ID)
                ->where('m.publish', 2)
                ->whereNull('m.deleted_at');

            if ($table === 'attributes') {
                $this->onlyBrandsWithProducts($query);
            }

            $rows = $query->orderByDesc('m.updated_at')
                ->select('r.canonical', 'm.updated_at')
                ->get();

            foreach ($rows as $row) {
                $urls[] = [
                    'loc' => write_url($row->canonical),
                    'lastmod' => $row->updated_at ? date('c', strtotime($row->updated_at)) : null,
                    'changefreq' => $changefreq,
                    'priority' => $priority,
                ];
            }
        }

        return $this->unique($urls);
    }

    public function render(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($this->urls() as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
            if ($url['lastmod']) {
                $xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            }
            $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= "</urlset>\n";
        return $xml;
    }

    /**
     * Ghi file, trả về số URL. Ghi ra file tạm rồi rename để bot không bao giờ
     * đọc phải file đang ghi dở.
     */
    public function generate(): int
    {
        $xml = $this->render();
        $path = public_path('sitemap.xml');
        $tmp = $path . '.' . getmypid() . '.tmp';
        file_put_contents($tmp, $xml);
        rename($tmp, $path);
        return substr_count($xml, '<url>');
    }

    /**
     * Dùng sau khi lưu dữ liệu trong admin: lỗi sinh sitemap không được làm hỏng
     * thao tác của người dùng.
     */
    public function generateQuietly(): void
    {
        try {
            $this->generate();
        } catch (\Throwable $e) {
            Log::error('Sitemap generate failed: ' . $e->getMessage());
        }
    }

    /**
     * Trang thương hiệu (AttributeController) chỉ có với thuộc tính nhóm thương
     * hiệu; trang không có sản phẩm nào đang bán thì không đưa vào sitemap.
     * Đếm giống hệt trang thương hiệu (BrandService), số thương hiệu ít nên
     * kiểm tra từng cái.
     */
    private function onlyBrandsWithProducts($query): void
    {
        $brandService = app(BrandService::class);
        $withProducts = [];
        $candidates = DB::table('attributes')
            ->where('attribute_catalogue_id', $brandService->catalogueId())
            ->where('publish', 2)
            ->whereNull('deleted_at')
            ->pluck('id');

        foreach ($candidates as $id) {
            [$sql, $bindings] = $brandService->productCondition($brandService->sameBrandIds($id), 'p');
            $exists = DB::table('products as p')
                ->where('p.publish', 2)
                ->whereNull('p.deleted_at')
                ->whereRaw($sql, $bindings)
                ->exists();
            if ($exists) {
                $withProducts[] = $id;
            }
        }

        $query->whereIn('m.id', $withProducts ?: [0]);
    }

    // Bỏ URL trùng, giữ bản xuất hiện đầu tiên.
    private function unique(array $urls): array
    {
        $seen = [];
        $result = [];
        foreach ($urls as $url) {
            if (isset($seen[$url['loc']])) {
                continue;
            }
            $seen[$url['loc']] = true;
            $result[] = $url;
        }
        return $result;
    }
}
