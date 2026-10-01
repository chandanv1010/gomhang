<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Trang danh mục bài viết ngoài website phải xếp bài MỚI NHẤT lên đầu.
 *
 * Trước đây danh sách chỉ sắp theo `posts.recommend`: gần như mọi bài đều có
 * cùng một giá trị nên thứ tự thực tế là thứ tự MySQL trả về - không liên quan
 * đến ngày đăng, và không ổn định giữa các trang phân trang.
 *
 * Test đọc dữ liệu đang có trong CSDL chứ không tự tạo bài, nên nó kiểm tra
 * đúng cái người dùng nhìn thấy. Danh mục nào chưa đủ hai bài thì bỏ qua.
 */
class PostCatalogueOrderTest extends TestCase
{
    /** Số bài một trang - khớp với PostService::paginate. */
    private const MOI_TRANG = 15;

    public function test_danh_muc_bai_viet_xep_bai_moi_nhat_len_dau(): void
    {
        $catalogue = $this->danhMucNhieuBaiNhat();

        if (is_null($catalogue)) {
            $this->markTestSkipped('Chua co danh muc bai viet nao co tu hai bai tro len.');
        }

        $mongDoi = $this->thuTuMongDoi($catalogue->id);

        $url = '/' . ltrim((string) $catalogue->canonical, '/') . config('apps.general.suffix');

        $html = $this->get($url)->assertOk()->getContent();

        $truoc = -1;

        foreach ($mongDoi as $ten) {
            $viTri = strpos($html, e($ten));

            $this->assertNotFalse($viTri, "Khong thay bai \"{$ten}\" tren trang danh muc");
            $this->assertGreaterThan(
                $truoc,
                $viTri,
                "Bai \"{$ten}\" dung sai cho - danh sach khong xep theo bai moi nhat"
            );

            $truoc = $viTri;
        }
    }

    /**
     * Danh mục đang hiển thị ngoài website và có nhiều bài nhất - chọn nó để
     * phép so thứ tự có ý nghĩa.
     */
    private function danhMucNhieuBaiNhat()
    {
        return DB::table('post_catalogues as pc')
            ->join('post_catalogue_language as pcl', function ($j) {
                $j->on('pcl.post_catalogue_id', '=', 'pc.id')->where('pcl.language_id', '=', 1);
            })
            ->join('post_catalogue_post as pcp', 'pcp.post_catalogue_id', '=', 'pc.id')
            ->join('posts as p', 'p.id', '=', 'pcp.post_id')
            // Chi lay danh muc that su co duong dan ngoai website, de $this->get()
            // roi dung PostCatalogueController chu khong phai 404.
            ->join('routers as r', function ($j) {
                $j->on('r.canonical', '=', 'pcl.canonical')
                    ->where('r.language_id', '=', 1)
                    ->where('r.controllers', '=', 'App\Http\Controllers\Frontend\PostCatalogueController');
            })
            ->whereNull('pc.deleted_at')
            ->whereNull('p.deleted_at')
            ->where('pc.publish', 2)
            ->whereNotIn('pcl.canonical', ['ve-chung-toi'])
            ->groupBy('pc.id', 'pcl.canonical')
            ->havingRaw('COUNT(p.id) >= 2')
            ->orderByRaw('COUNT(p.id) DESC')
            ->select('pc.id', 'pcl.canonical')
            ->first();
    }

    /**
     * Thứ tự đúng của trang 1 - dùng lại đúng phép lọc theo cây danh mục của
     * PostService::whereRaw (lft/rgt) để tính cả bài của danh mục con.
     */
    private function thuTuMongDoi(int $catalogueId)
    {
        return DB::table('posts as p')
            ->join('post_language as pl', function ($j) {
                $j->on('pl.post_id', '=', 'p.id')->where('pl.language_id', '=', 1);
            })
            ->join('post_catalogue_post as pcp', 'pcp.post_id', '=', 'p.id')
            ->whereNull('p.deleted_at')
            ->whereRaw(
                'pcp.post_catalogue_id IN (
                    SELECT id FROM post_catalogues
                    WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                      AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
                )',
                [$catalogueId, $catalogueId]
            )
            ->groupBy('p.id', 'pl.name', 'p.created_at')
            ->orderByDesc('p.created_at')
            ->orderByDesc('p.id')
            ->limit(self::MOI_TRANG)
            ->pluck('pl.name');
    }
}
