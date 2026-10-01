<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Giỏ hàng mở cho khách chưa đăng nhập.
 *
 * Trước đây nhóm route giỏ hàng nằm sau middleware `customer_auth`: bấm "giỏ
 * hàng" là bị đẩy sang trang đăng nhập, mất đơn của người mua lần đầu. Bộ test
 * này giữ cho cửa đó không bị đóng lại, và giữ cho trang giỏ hàng không vỡ khi
 * `$buyer` là null - khối điểm tích luỹ đọc `$buyer->point` nên phải ẩn đi.
 */
class CartGuestCheckoutTest extends TestCase
{
    private const EMAIL = 'phpunit.cart@example.test';
    private const PASSWORD = 'PhpUnit@2026';

    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();

        DB::beginTransaction();

        $this->customerId = DB::table('customers')->insertGetId([
            'customer_catalogue_id' => (int) (DB::table('customer_catalogues')->value('id') ?? 1),
            'code' => 'PU' . random_int(1000, 9999),
            'name' => 'PhpUnit Cart',
            'phone' => '0900000456',
            'email' => self::EMAIL,
            'password' => Hash::make(self::PASSWORD),
            'publish' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }

    public function test_khach_chua_dang_nhap_mo_duoc_gio_hang(): void
    {
        $this->get('/gio-hang.html')->assertOk();
    }

    /**
     * Điểm tích luỹ gắn với tài khoản. Với khách vãng lai chỉ mời đăng nhập,
     * không hiện ô nhập điểm - ajax/cart/checkPoint vẫn cần đăng nhập.
     */
    public function test_khach_chua_dang_nhap_khong_thay_o_nhap_diem(): void
    {
        $this->get('/gio-hang.html')
            ->assertOk()
            ->assertDontSee('id="point_redeem"', false);
    }

    public function test_khach_da_dang_nhap_van_thay_o_nhap_diem(): void
    {
        $this->actingAs($this->customer(), 'customer')
            ->get('/gio-hang.html')
            ->assertOk()
            ->assertSee('id="point_redeem"', false);
    }

    /**
     * /thanh-toan.html mua nhanh một sản phẩm. Giỏ 'pay' rỗng thì quay về giỏ
     * hàng - trước đây khách chưa đăng nhập bị đẩy sang trang đăng nhập.
     */
    public function test_khach_chua_dang_nhap_vao_thanh_toan_thi_ve_gio_hang_chu_khong_phai_trang_dang_nhap(): void
    {
        $this->get('/thanh-toan.html')->assertRedirect(route('cart.checkout'));
    }

    /**
     * Đổi điểm vẫn là phần riêng của tài khoản, không được mở theo giỏ hàng.
     */
    public function test_doi_diem_van_doi_dang_nhap(): void
    {
        $this->postJson('/ajax/cart/checkPoint', ['point' => 10])
            ->assertStatus(401);
    }

    private function customer()
    {
        return \App\Models\Customer::find($this->customerId);
    }
}
