<?php

namespace App\Services\V1\Core;

use App\Enums\VoucherEnum;
use App\Models\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use RuntimeException;

/**
 * Voucher o gio hang - mot cho duy nhat kiem tra dieu kien va tinh tien giam.
 *
 * Moi don chi dung 1 voucher. Session chi luu id voucher; so tien giam luon
 * tinh lai tu gio hang hien tai (doi so luong, xoa san pham, het han... thi
 * voucher tu bi go va bao ly do), nen so hien thi va so luu vao don luon khop.
 *
 * Voucher can dang nhap: gioi han "so lan dung / khach" va bang voucher_usages
 * (customer_id NOT NULL) deu gan voi tai khoan.
 *
 * $ctx = [
 *   'items'             => Collection cac dong gio hang (da qua CartService::remakeCart),
 *   'subtotal'          => tong price * qty,
 *   'promotionDiscount' => giam gia khuyen mai theo tong don,
 *   'ship'              => phi giao hang,
 * ]
 */
class CartVoucherService
{
    public const SESSION_KEY = 'cart_voucher_id';

    /** Ly do voucher trong session bi go o lan tinh gan nhat (de bao cho khach). */
    public ?string $lastDropReason = null;

    public function context($items, float $promotionDiscount, float $ship): array
    {
        $items = collect($items);
        $subtotal = $items->sum(fn ($item) => (float) $item->price * (int) $item->qty);

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'promotionDiscount' => $promotionDiscount,
            'ship' => $ship,
        ];
    }

    /** Voucher hien con hieu luc cho khach (chua xet gio hang). */
    public function availableFor($customer): Collection
    {
        if (!$customer) {
            return collect();
        }

        return $this->baseQuery()
            ->with(['voucher_order_conditions', 'voucher_shipping_conditions'])
            ->orderBy('end_date')
            ->get()
            ->filter(fn (Voucher $v) => $this->usedByCustomer($v, $customer) < max(1, (int) $v->max_usage_per_user))
            ->values();
    }

    /**
     * Danh sach voucher cho trang gio hang, kem so tien se giam (hoac ly do chua
     * dung duoc) voi gio hang hien tai.
     */
    public function listForCart($customer, array $ctx): Collection
    {
        return $this->availableFor($customer)->map(function (Voucher $v) use ($ctx) {
            try {
                $v->preview_discount = $this->discount($v, $ctx);
                $v->preview_error = null;
            } catch (RuntimeException $e) {
                $v->preview_discount = 0;
                $v->preview_error = $e->getMessage();
            }
            return $v;
        });
    }

    /** Ap voucher theo id (bam vao danh sach) hoac theo ma (go vao o nhap). */
    public function apply($voucherIdOrCode, $customer, array $ctx): array
    {
        if (!$customer) {
            throw new RuntimeException('Vui lòng đăng nhập để sử dụng mã giảm giá.');
        }

        $voucher = $this->find($voucherIdOrCode);
        $discount = $this->validate($voucher, $customer, $ctx);

        Session::put(self::SESSION_KEY, $voucher->id);

        return ['voucher' => $voucher, 'discount' => $discount];
    }

    public function forget(): void
    {
        Session::forget(self::SESSION_KEY);
        // Du lieu voucher kieu cu (truoc khi viet lai) - don dep neu con sot.
        Session::forget('voucher');
    }

    /**
     * Voucher dang ap trong session, tinh lai voi gio hang hien tai.
     * Khong con hop le thi go khoi session va ghi ly do vao $lastDropReason.
     */
    public function current($customer, array $ctx): ?array
    {
        $this->lastDropReason = null;
        $id = Session::get(self::SESSION_KEY);
        if (!$id) {
            return null;
        }

        try {
            if (!$customer) {
                throw new RuntimeException('Vui lòng đăng nhập để sử dụng mã giảm giá.');
            }
            $voucher = $this->find($id);
            $discount = $this->validate($voucher, $customer, $ctx);
            return ['voucher' => $voucher, 'discount' => $discount];
        } catch (RuntimeException $e) {
            $this->forget();
            $this->lastDropReason = $e->getMessage();
            return null;
        }
    }

    /**
     * Ghi nhan da dung voucher cho don (trong transaction tao don).
     * Khoa dong voucher de 2 don dong thoi khong vuot so luong.
     */
    public function recordUsage($order, Voucher $voucher, $customer): void
    {
        $locked = Voucher::whereKey($voucher->id)->lockForUpdate()->first();
        if (!$locked || (int) $locked->used_quantity >= (int) $locked->total_quantity) {
            throw new RuntimeException('Mã giảm giá ' . $voucher->code . ' vừa hết lượt sử dụng.');
        }

        $locked->increment('used_quantity');

        $now = now();
        DB::table('voucher_orders')->insert([
            'order_id' => $order->id,
            'voucher_id' => $voucher->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($customer) {
            DB::table('voucher_usages')->insert([
                'order_id' => $order->id,
                'voucher_id' => $voucher->id,
                'customer_id' => $customer->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->forget();
    }

    // ------------------------------------------------------------------

    private function baseQuery()
    {
        return Voucher::query()
            ->whereColumn('used_quantity', '<', 'total_quantity')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    private function find($voucherIdOrCode): Voucher
    {
        $value = trim((string) $voucherIdOrCode);
        if ($value === '') {
            throw new RuntimeException('Bạn chưa nhập mã giảm giá.');
        }

        $voucher = Voucher::with(['voucher_order_conditions', 'voucher_shipping_conditions', 'voucher_products'])
            ->where(function ($q) use ($value) {
                // Khach hay go chu thuong; khong phu thuoc collation cua cot code.
                $q->whereRaw('UPPER(code) = ?', [mb_strtoupper($value)]);
                if (ctype_digit($value)) {
                    $q->orWhere('id', (int) $value);
                }
            })
            ->first();

        if (!$voucher) {
            throw new RuntimeException('Mã giảm giá không tồn tại.');
        }

        return $voucher;
    }

    private function usedByCustomer(Voucher $voucher, $customer): int
    {
        return DB::table('voucher_usages')
            ->where('voucher_id', $voucher->id)
            ->where('customer_id', $customer->id)
            ->count();
    }

    /** Kiem tra day du dieu kien, tra ve so tien giam. */
    private function validate(Voucher $voucher, $customer, array $ctx): float
    {
        $now = now();
        if ($voucher->start_date && $now->lt($voucher->start_date)) {
            throw new RuntimeException('Mã giảm giá chưa đến thời gian áp dụng.');
        }
        if ($voucher->end_date && $now->gt($voucher->end_date)) {
            throw new RuntimeException('Mã giảm giá đã hết hạn.');
        }
        if ((int) $voucher->used_quantity >= (int) $voucher->total_quantity) {
            throw new RuntimeException('Mã giảm giá đã hết lượt sử dụng.');
        }
        if ($this->usedByCustomer($voucher, $customer) >= max(1, (int) $voucher->max_usage_per_user)) {
            throw new RuntimeException('Bạn đã dùng hết số lần cho phép của mã giảm giá này.');
        }

        return $this->discount($voucher, $ctx);
    }

    /** Tinh so tien giam theo loai voucher. Khong du dieu kien thi throw. */
    public function discount(Voucher $voucher, array $ctx): float
    {
        if ($ctx['items']->isEmpty()) {
            throw new RuntimeException('Giỏ hàng đang trống.');
        }

        switch ($voucher->product_scope) {
            case VoucherEnum::TOTAL_ORDERS:
                $base = max(0, $ctx['subtotal'] - $ctx['promotionDiscount']);
                $min = (float) ($voucher->voucher_order_conditions->min_order_value ?? 0);
                if ($base < $min) {
                    throw new RuntimeException('Đơn hàng cần tối thiểu ' . number_format($min, 0, ',', '.') . 'đ để dùng mã này.');
                }
                if ($ctx['promotionDiscount'] > 0 && !$voucher->combine_promotion) {
                    throw new RuntimeException('Mã này không dùng chung với chương trình khuyến mại đang áp dụng.');
                }
                return min($base, $this->amount($voucher, $base));

            case VoucherEnum::SHIPPING_ORDERS:
                $ship = (float) $ctx['ship'];
                if ($ship <= 0) {
                    throw new RuntimeException('Đơn hàng hiện không có phí giao hàng để giảm.');
                }
                $min = (float) ($voucher->voucher_shipping_conditions->min_shipping_value ?? 0);
                if ($ship < $min) {
                    throw new RuntimeException('Phí giao hàng cần từ ' . number_format($min, 0, ',', '.') . 'đ để dùng mã này.');
                }
                return min($ship, $this->amount($voucher, $ship));

            case VoucherEnum::ALL_PRODUCTS:
            case VoucherEnum::SPECIFIC_PRODUCTS:
                $productIds = $voucher->product_scope === VoucherEnum::SPECIFIC_PRODUCTS
                    ? $voucher->voucher_products->pluck('id')->map(fn ($id) => (int) $id)->all()
                    : null;

                $total = 0;
                $matched = false;
                $blockedByPromotion = false;
                foreach ($ctx['items'] as $item) {
                    $productId = (int) explode('_', (string) $item->id)[0];
                    if ($productIds !== null && !in_array($productId, $productIds, true)) {
                        continue;
                    }
                    $matched = true;
                    $price = (float) $item->price;
                    $original = (float) ($item->priceOriginal ?? $price);
                    if ($price < $original && !$voucher->combine_promotion) {
                        $blockedByPromotion = true;
                        continue;
                    }
                    $perUnit = min($price, $this->amount($voucher, $price));
                    $total += $perUnit * (int) $item->qty;
                }

                if (!$matched) {
                    throw new RuntimeException('Giỏ hàng không có sản phẩm áp dụng mã này.');
                }
                if ($total <= 0 && $blockedByPromotion) {
                    throw new RuntimeException('Mã này không dùng chung với sản phẩm đang khuyến mại.');
                }
                return $total;
        }

        throw new RuntimeException('Mã giảm giá không hợp lệ.');
    }

    /** Tien giam tren mot gia tri, co ap tran max_discount_amount (0/null = khong gioi han). */
    private function amount(Voucher $voucher, float $base): float
    {
        $value = (float) $voucher->discount_value;
        $amount = $voucher->discount_type === VoucherEnum::PERCENTAGE ? $base * $value / 100 : $value;
        $cap = (float) ($voucher->max_discount_amount ?? 0);
        if ($cap > 0) {
            $amount = min($amount, $cap);
        }
        return round(max(0, $amount));
    }
}
