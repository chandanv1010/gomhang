<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The cart and the customer account area are behind the `customer_auth`
 * middleware. These tests pin the guard itself plus the two things that made it
 * unhelpful in practice: it dumped people on the homepage after login instead of
 * where they were going, and the reason it gave for the redirect was never shown.
 */
class CustomerAuthGuardTest extends TestCase
{
    private const EMAIL = 'phpunit.guard@example.test';
    private const PASSWORD = 'PhpUnit@2026';

    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();

        DB::beginTransaction();

        $this->customerId = DB::table('customers')->insertGetId([
            'customer_catalogue_id' => (int) (DB::table('customer_catalogues')->value('id') ?? 1),
            'code' => 'PU' . random_int(1000, 9999),
            'name' => 'PhpUnit Guard',
            'phone' => '0900000123',
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

    public static function protectedUrls(): array
    {
        return [
            'cart' => ['/gio-hang.html'],
            // /thanh-toan.html needs a product in the 'pay' cart instance, so it is
            // covered separately below rather than as a plain 200.
            'account' => ['/tai-khoan.html'],
            'change password' => ['/tai-khoan/thay-doi-mat-khau.html'],
            'order history' => ['/tai-khoan/lich-su-don-hang.html'],
        ];
    }

    /** @dataProvider protectedUrls */
    public function test_a_guest_is_sent_to_the_login_page(string $url): void
    {
        $this->get($url)->assertRedirect(route('customer.login'));
    }

    /** @dataProvider protectedUrls */
    public function test_a_signed_in_customer_can_open_the_page(string $url): void
    {
        $this->actingAs($this->customer(), 'customer')->get($url)->assertOk();
    }

    public function test_the_guard_explains_why_it_redirected(): void
    {
        $this->get('/gio-hang.html')->assertSessionHas(\App\Http\Middleware\CustomerAuth::NOTICE_KEY);
    }

    public function test_the_login_page_shows_that_explanation(): void
    {
        $this->get('/gio-hang.html');

        $this->get(route('customer.login'))
            ->assertOk()
            ->assertSee('Vui lòng đăng nhập', false);
    }

    public function test_login_returns_the_customer_to_the_page_they_wanted(): void
    {
        $this->get('/gio-hang.html')->assertRedirect(route('customer.login'));

        $this->post(route('customer.dologin'), [
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ])->assertRedirect(url('/gio-hang.html'));
    }

    public function test_login_without_an_intended_page_lands_on_the_homepage(): void
    {
        $this->post(route('customer.dologin'), [
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ])->assertRedirect(route('home.index'));
    }

    public function test_an_ajax_call_gets_401_json_rather_than_a_login_page(): void
    {
        $this->getJson('/gio-hang.html')
            ->assertStatus(401)
            ->assertJsonPath('code', 401);
    }

    /**
     * /thanh-toan.html checks out a single product held in the 'pay' cart
     * instance. With nothing there the view used to fatal on a null product.
     */
    public function test_checkout_without_a_product_redirects_to_the_cart_instead_of_erroring(): void
    {
        $this->actingAs($this->customer(), 'customer')
            ->get('/thanh-toan.html')
            ->assertRedirect(route('cart.checkout'));
    }

    public function test_a_guest_cannot_reach_checkout(): void
    {
        $this->get('/thanh-toan.html')->assertRedirect(route('customer.login'));
    }

    private function customer()
    {
        return \App\Models\Customer::find($this->customerId);
    }
}
