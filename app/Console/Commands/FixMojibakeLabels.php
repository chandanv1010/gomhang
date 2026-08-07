<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Dựng lại các nhãn tiếng Việt bị lỗi phông hiện trong admin.
 *
 * Cùng nguyên nhân với tên quyền: dữ liệu từng bị ghi qua kết nối latin1 nên mỗi
 * byte tiếng Việt thành một dấu "?" ("Quản trị viên" -> "Qu???n tr??? vi??n").
 * Chữ gốc mất hẳn nên phải chép lại bằng tay - may là các nhãn này đều đoán được
 * chắc chắn từ phần chữ còn sót.
 *
 * Khác lệnh sửa quyền ở chỗ không suy ra được từ cột nào, nên dùng bảng tra cứu
 * theo id. Chỉ ghi đè dòng đang hỏng.
 */
class FixMojibakeLabels extends Command
{
    protected $signature = 'sua-loi-phong:nhan
                            {--dry-run : Chỉ liệt kê, không ghi}
                            {--an-menu-virex : Ẩn luôn các mục menu còn lại của VIREX}';

    protected $description = 'Dựng lại nhãn tiếng Việt bị lỗi phông trong admin';

    /**
     * bảng => [cột => [id => giá trị đúng]]
     *
     * @var array<string, array{cot: string, gia_tri: array<int, string>}>
     */
    private const BANG = [
        'menu_catalogues' => ['cot' => 'name', 'gia_tri' => [
            1 => 'Menu chính',
            2 => 'Menu chân trang',
        ]],
        'user_catalogues' => ['cot' => 'name', 'gia_tri' => [
            1 => 'Quản trị viên',
            2 => 'Cộng tác viên',
        ]],
        'customer_catalogues' => ['cot' => 'name', 'gia_tri' => [
            1 => 'Khách Lẻ',
            2 => 'Xưởng',
            3 => 'Đại Lý',
            5 => 'Khách hàng B2B',
        ]],
        'languages' => ['cot' => 'name', 'gia_tri' => [
            1 => 'Tiếng Việt',
            2 => 'Tiếng Anh',
            3 => 'Tiếng Trung',
        ]],
        'sources' => ['cot' => 'name', 'gia_tri' => [
            4 => 'Khách hàng offline',
        ]],
        'widgets' => ['cot' => 'name', 'gia_tri' => [
            5 => 'Phản hồi của khách hàng',
            // Nhãn cũ là câu quảng cáo của VIREX, đặt lại theo đúng vai trò widget.
            9 => 'Giới thiệu về chúng tôi',
            11 => 'Giải pháp',
            12 => 'Sản phẩm',
            14 => 'Dự án tiêu biểu',
            16 => 'Thư viện Video',
        ]],
        'slides' => ['cot' => 'name', 'gia_tri' => [
            11 => 'Slide chính',
            12 => 'Đội ngũ giáo viên',
            14 => 'Tại sao nên chọn chúng tôi',
            15 => 'Slide đối tác',
        ]],
    ];

    /** Nhãn menu: tra theo chuỗi hỏng vì id thay đổi giữa các lần dựng lại menu. */
    private const MENU = [
        'Ch??nh s??ch' => 'Chính sách',
        '??i???u kho???n s??? d???ng' => 'Điều khoản sử dụng',
        'Danh m???c' => 'Danh mục',
        'Ch??nh s??ch b???o m???t' => 'Chính sách bảo mật',
        'Ph????ng th???c v???n chuy???n' => 'Phương thức vận chuyển',
        'Gi???i quy???t khi???u n???i' => 'Giải quyết khiếu nại',
        'Ch??nh s??ch ki???m h??ng' => 'Chính sách kiểm hàng',
        'Li??n k???t' => 'Liên kết',
    ];

    public function handle(): int
    {
        $hong = '[[:alpha:]][?]{2,}|[?]{2,}[[:alpha:]]';
        $sua = 0;
        $khan = $this->option('dry-run');

        foreach (self::BANG as $bang => $dinhNghia) {
            foreach ($dinhNghia['gia_tri'] as $id => $giaTri) {
                $cu = DB::table($bang)->where('id', $id)->value($dinhNghia['cot']);

                if ($cu === null || !preg_match('#[a-zA-Z]\?{2,}|\?{2,}[a-zA-Z]#', $cu)) {
                    continue;   // không hỏng thì không đụng
                }

                $this->line(sprintf('  %-20s #%-3s %s', $bang, $id, $giaTri));

                if (!$khan) {
                    DB::table($bang)->where('id', $id)->update([$dinhNghia['cot'] => $giaTri]);
                }
                $sua++;
            }
        }

        // Nhãn menu tra theo chuỗi, chỉ sửa mục còn dùng.
        foreach (self::MENU as $cu => $moi) {
            $ids = DB::table('menu_language')->where('name', $cu)->pluck('menu_id');

            if ($ids->isEmpty()) {
                continue;
            }

            $this->line(sprintf('  %-20s %d mục  %s', 'menu_language', $ids->count(), $moi));

            if (!$khan) {
                DB::table('menu_language')->where('name', $cu)->update(['name' => $moi]);
            }
            $sua += $ids->count();
        }

        $this->newLine();
        $this->info($khan ? "Sẽ sửa {$sua} nhãn (chạy thử)." : "Đã sửa {$sua} nhãn.");

        // Còn sót là các mục của VIREX: ống nước, van, PCCC và mấy câu quảng cáo
        // chạy ngang. Không dựng lại vì khác ngành hàng, và không nhóm nào trong
        // số này được frontend dùng tới.
        $conLai = DB::table('menu_language')
            ->join('menus', 'menus.id', '=', 'menu_language.menu_id')
            ->whereRaw("menu_language.name REGEXP '{$hong}'")
            ->whereNull('menus.deleted_at');

        $soConLai = (clone $conLai)->count();

        if ($soConLai === 0) {
            return self::SUCCESS;
        }

        if (!$this->option('an-menu-virex')) {
            $this->warn("Còn {$soConLai} mục menu của VIREX (ống nước, van, PCCC) vẫn hiện trong admin.");
            $this->line('  Thêm --an-menu-virex để ẩn chúng đi (xoá mềm, khôi phục lại được).');

            return self::SUCCESS;
        }

        $ids = (clone $conLai)->pluck('menus.id');

        if (!$khan) {
            DB::table('menus')->whereIn('id', $ids)->update(['deleted_at' => now()]);
        }

        $this->info("Đã ẩn {$soConLai} mục menu của VIREX (xoá mềm, khôi phục lại được).");

        return self::SUCCESS;
    }
}
