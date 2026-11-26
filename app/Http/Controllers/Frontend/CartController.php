<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\User\ProvinceRepository;
use App\Repositories\Product\PromotionRepository;
use App\Repositories\Core\OrderRepository;
use App\Repositories\Product\VoucherRepository;
use App\Repositories\Product\ProductRepository;
use App\Services\V1\Product\VoucherService;
use App\Services\V1\Core\WidgetService;
use App\Services\V1\Core\ContactService;
use App\Services\V1\Core\CartService;

use App\Http\Requests\StoreCartRequest;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Classes\Vnpay;
use App\Classes\Momo;
use App\Classes\Paypal;
use App\Classes\Zalo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Facades\Agent;

class CartController extends FrontendController
{
  
    protected $provinceRepository;
    protected $productRepository;
    protected $promotionRepository;
    protected $orderRepository;
    protected $voucherRepository;
    protected $cartService;
    protected $widgetService;
    protected $voucherService;
    protected $contactService;
    protected $vnpay;
    protected $momo;
    protected $paypal;
    protected $zalo;
    protected $token;

    public function __construct(
        ProvinceRepository $provinceRepository,
        ProductRepository $productRepository,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,
        VoucherRepository $voucherRepository,
        VoucherService $voucherService,
        CartService $cartService,
        WidgetService $widgetService,
        ContactService $contactService,
        Vnpay $vnpay,
        Momo $momo,
        Paypal $paypal,
        Zalo $zalo,
    ){
       
        $this->provinceRepository = $provinceRepository;
        $this->productRepository = $productRepository;
        $this->promotionRepository = $promotionRepository;
        $this->orderRepository = $orderRepository;
        $this->voucherRepository = $voucherRepository;
        $this->cartService = $cartService;
        $this->voucherService = $voucherService;
        $this->widgetService = $widgetService;
        $this->contactService = $contactService;
        $this->vnpay = $vnpay;
        $this->momo = $momo;
        $this->paypal = $paypal;
        $this->zalo = $zalo;
        $this->token = getGiaoHangNhanhToken();
        parent::__construct();
    }


    private function getBuyer(){
        return Auth::guard('customer')->user();
    }

   
    public function checkout(){

        $provinces = $this->provinceRepository->all();

        $buyer = $this->getBuyer();

        // Gio hang, khuyen mai, phi ship, voucher - dung chung voi ajax voucher
        // (CartService::checkoutData) de so tren trang va so khi ap ma luon khop.
        extract($this->cartService->checkoutData($buyer));

        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('thanh-toan', TRUE, TRUE),
        ];

        $system = $this->system;

        $config = $this->config();

        // No resources/views/mobile directory exists, so the mobile branch that
        // used to be here threw "View [mobile.cart.index] not found" on every
        // phone. The frontend template is responsive; serve it to all devices.
        $template = 'frontend.cart.index';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'provinces',
            'carts',
            'cartPromotion',
            'cartCaculate',
            'allVoucherTotal',
            'appliedVoucher',
            'voucherDiscount',
            'voucherDropReason',
            'totalVoucherProduct',
            'voucher',
            'shipping',
            'discountTotalProduct',
            'buyer'
        ));
        
    }

    public function pay(){

        $carts = Cart::instance('pay')->content();


        $product_id = null;

        foreach($carts as $item){
            $product_id = $item->id;
        }

        $product = null;
        if($product_id){
            $product = $this->productRepository->findById($product_id);

        }

        // This page checks out one product picked with "Mua ngay", held in the
        // 'pay' cart instance. Opened directly - or after that instance expired -
        // there is nothing to pay for, and the view used to fatal on a null
        // product. Send them to the cart instead.
        if(is_null($product)){
            return redirect()->route('cart.checkout')
                ->with('error', 'Không có sản phẩm nào để thanh toán. Vui lòng chọn sản phẩm.');
        }

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'showroom-system','object' => true],
        ], $this->language);

        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('thanh-toan', TRUE, TRUE),
        ];

        $system = $this->system;

        $config = $this->config();

        // No resources/views/mobile directory exists, so the mobile branch that
        // used to be here threw "View [mobile.cart.pay] not found" on every
        // phone. The frontend template is responsive; serve it to all devices.
        $template = 'frontend.cart.pay';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'product',
            'widgets'
        ));
        
    }

    public function storePay(StoreCartRequest $request){
        if($this->contactService->create($request)){
            return redirect()->route('cart.pay')->with('success','Gửi yêu cầu thành công . Chúng tôi sẽ sớm liên hệ với bạn');
        }
        return redirect()->route('cart.pay')->with('error','Gửi yêu cầu không thành công. Hãy thử lại');
    }


    public function store(StoreCartRequest $request){
        $buyer = $this->getBuyer();
        $system = $this->system;
        // Tinh lai voucher dang ap voi gio hang luc bam dat hang; neu khong con
        // hop le thi khong dat hang ma quay lai bao ly do de khach thay so tien moi.
        $voucherService = app(\App\Services\V1\Core\CartVoucherService::class);
        $appliedVoucher = $voucherService->current($buyer, $this->cartService->voucherContext($buyer));
        if(is_null($appliedVoucher) && $voucherService->lastDropReason){
            return redirect()->route('cart.checkout')->withInput()
                ->withErrors(['voucher' => 'Mã giảm giá đã được gỡ: ' . $voucherService->lastDropReason . ' Vui lòng kiểm tra lại tổng tiền.']);
        }

        $orders = $this->cartService->order($request, $system, $buyer, $appliedVoucher);
        if($orders['flag']){
            return redirect()->route('cart.success')->with('success','Đặt hàng thành công');
        }
        return redirect()->route('cart.checkout')->withInput()
            ->withErrors(['order' => $orders['message'] ?? 'Đặt hàng không thành công. Hãy thử lại']);
    }

    public function success(){

        $orderSummary = session('orderSummary');
        if(!$orderSummary){
            return redirect()->route('home.index')->with('error', 'Có vấn đề xảy ra trong quá trình đặt hàng. Hãy thử lại sau');
        }
        
        $seo = [
            'meta_title' => 'Đặt đơn hàng thành công',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('cart/success', TRUE, TRUE),
        ];
        $system = $this->system;
        $config = $this->config();

        
        // No resources/views/mobile directory exists, so the mobile branch that
        // used to be here threw "View [mobile.cart.success] not found" on every
        // phone. The frontend template is responsive; serve it to all devices.
        $template = 'frontend.cart.success';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'orderSummary'
        ));
    }

    public function paymentMethod($order = null){
        $class = $order['order']->method;
        $response = $this->{$class}->payment($order['order']);
        return $response;
    }

    
    private function config(){
        return [
            'language' => $this->language,
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'frontend/core/library/cart.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'buyer/resources/buyer.js'
            ]
        ];
    }
  

}
