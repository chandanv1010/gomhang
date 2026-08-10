<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Bỏ ON UPDATE CURRENT_TIMESTAMP khỏi cột promotions.startDate.
 *
 * Cột này đang là:
 *   startDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
 *
 * Nên MỌI lần cập nhật bản ghi khuyến mãi, MySQL tự ghi lại ngày bắt đầu thành
 * thời điểm hiện tại - bất kể ứng dụng gửi giá trị gì. Chỉ cần bấm công tắc
 * bật/tắt là ngày bắt đầu nhảy về "bây giờ", nên không thể hẹn trước lịch chạy
 * khuyến mãi.
 *
 * Đây là hành vi mặc định của MySQL với cột TIMESTAMP đầu tiên trong bảng khi
 * migration gốc không khai báo rõ default. Sửa bằng cách khai báo lại cột, giữ
 * nguyên kiểu và dữ liệu, chỉ gỡ hai thuộc tính tự sinh.
 *
 * Không dùng Schema::table()->change() vì doctrine/dbal hay bỏ mất phần
 * ON UPDATE; viết thẳng ALTER TABLE cho chắc chắn.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (!$this->coCotTuGhiLai()) {
            return;   // đã sửa rồi thì bỏ qua, chạy lại migration không sao
        }

        DB::statement('ALTER TABLE `promotions` MODIFY `startDate` TIMESTAMP NULL DEFAULT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            'ALTER TABLE `promotions` MODIFY `startDate` TIMESTAMP NOT NULL '
            . 'DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        );
    }

    private function coCotTuGhiLai(): bool
    {
        $cot = DB::selectOne(
            'SELECT EXTRA FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['promotions', 'startDate']
        );

        return $cot !== null && stripos((string) $cot->EXTRA, 'on update CURRENT_TIMESTAMP') !== false;
    }
};
