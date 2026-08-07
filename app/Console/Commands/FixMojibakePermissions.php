<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Dựng lại tên quyền bị lỗi phông trong bảng `permissions`.
 *
 * Dữ liệu tiếng Việt từng bị ghi qua kết nối đặt bảng mã latin1, MySQL thay mỗi
 * byte không biểu diễn được bằng dấu "?" nên "Xem danh sách nhóm bài viết" thành
 * "Xem danh s??ch nh??m b??i vi???t". Chữ gốc mất hẳn, không có phép toán
 * nào lấy lại được.
 *
 * Nhưng cột `canonical` chỉ toàn ASCII nên còn nguyên (`post.catalogue.index`),
 * và tên quyền vốn chỉ là "<hành động> <module>" nên dựng lại chính xác được.
 *
 * Chỉ đụng vào dòng đang hỏng, dòng nào lành thì để yên.
 */
class FixMojibakePermissions extends Command
{
    protected $signature = 'sua-loi-phong:quyen {--dry-run : Chỉ liệt kê, không ghi}';

    protected $description = 'Dựng lại tên quyền bị lỗi phông từ cột canonical';

    private const HANH_DONG = [
        'index' => 'Xem danh sách',
        'create' => 'Tạo mới',
        'update' => 'Sửa',
        'edit' => 'Sửa',
        'destroy' => 'Xóa',
        'translate' => 'Dịch',
        'all' => 'Toàn quyền',
        'permission' => 'Phân quyền',
    ];

    private const MODULE = [
        'area' => 'khu vực',
        'attribute' => 'thuộc tính',
        'attribute.catalogue' => 'nhóm thuộc tính',
        'city' => 'tỉnh thành',
        'contact' => 'liên hệ',
        'customer' => 'khách hàng',
        'customer.catalogue' => 'nhóm khách hàng',
        'distribution' => 'nhà phân phối',
        'distribution.area' => 'khu vực phân phối',
        'gallery.catalogue' => 'nhóm thư viện ảnh',
        'generate' => 'tiện ích',
        'language' => 'ngôn ngữ',
        'major' => 'ngành học',
        'major_catalogue' => 'nhóm ngành học',
        'major_group' => 'khối ngành',
        'menu' => 'menu',
        'order' => 'đơn hàng',
        'permission' => 'quyền',
        'policy' => 'chính sách',
        'post' => 'bài viết',
        'post.catalogue' => 'nhóm bài viết',
        'product' => 'sản phẩm',
        'product.catalogue' => 'nhóm sản phẩm',
        'project' => 'dự án',
        'promotion' => 'khuyến mãi',
        'review' => 'đánh giá',
        'scholarship' => 'học bổng',
        'scholarship.catalogue' => 'nhóm học bổng',
        'school' => 'trường học',
        'school_catalogue' => 'nhóm trường học',
        'slide' => 'slide',
        'source' => 'nguồn',
        'train' => 'đào tạo',
        'user' => 'thành viên',
        'user.catalogue' => 'nhóm thành viên',
        'voucher' => 'mã giảm giá',
        'widget' => 'widget',
    ];

    public function handle(): int
    {
        // Dấu hiệu lỗi phông: hai dấu "?" trở lên dính vào chữ cái. Khác hẳn dấu
        // hỏi bình thường trong câu.
        $hongRegex = '[[:alpha:]][?]{2,}|[?]{2,}[[:alpha:]]';

        $rows = DB::table('permissions')
            ->whereRaw("name REGEXP '{$hongRegex}'")
            ->orderBy('canonical')
            ->get(['id', 'name', 'canonical']);

        if ($rows->isEmpty()) {
            $this->info('Không có tên quyền nào bị lỗi phông.');
            return self::SUCCESS;
        }

        $this->info("Tìm thấy {$rows->count()} tên quyền bị lỗi.");

        $sua = 0;
        $khong = [];

        foreach ($rows as $row) {
            $ten = $this->dungTen($row->canonical);

            if ($ten === null) {
                $khong[] = $row->canonical;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line(sprintf('  %-26s %s', $row->canonical, $ten));
            } else {
                DB::table('permissions')->where('id', $row->id)
                    ->update(['name' => $ten, 'updated_at' => now()]);
            }

            $sua++;
        }

        $this->newLine();
        $this->info($this->option('dry-run') ? "Sẽ sửa {$sua} dòng (chạy thử)." : "Đã sửa {$sua} dòng.");

        if ($khong !== []) {
            $this->warn(count($khong) . ' dòng không dựng được tên, để nguyên:');
            foreach ($khong as $c) {
                $this->line('  ' . $c);
            }
        }

        return self::SUCCESS;
    }

    /** canonical "post.catalogue.index" -> "Xem danh sách nhóm bài viết" */
    private function dungTen(string $canonical): ?string
    {
        $phan = explode('.', $canonical);
        $hanhDong = array_pop($phan);
        $module = implode('.', $phan);

        if (!isset(self::HANH_DONG[$hanhDong], self::MODULE[$module])) {
            return null;
        }

        return self::HANH_DONG[$hanhDong] . ' ' . self::MODULE[$module];
    }
}
