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

        $carts = Cart::instance('shopping')->content();

        $carts = $this->cartService->remakeCart($carts);

        $cartCaculate = $this->cartService->reCaculateCart();

        $cartPromotion = $this->cartService->cartPromotion($cartCaculate['cartTotal']);

        $discountTotalProduct = 0;

        foreach ($carts as $cart) {
            $discountTotalProduct += ($cart->priceOriginal - $cart->price) * $cart->qty;
        }
 
        $buyer = $this->getBuyer();
        $shipping = $this->cartService->totalShipping($buyer);
        $totalVoucherProduct = $this->cartService->totalDiscountVoucher($carts);
        $voucher= Session::get('voucher') ?? null;
        $allVoucherTotal = null;
        if(!is_null($shipping) && !is_null($buyer)){
            $allVoucherTotal = $this->voucherService->listVoucher(($cartCaculate['cartTotal'] - $totalVoucherProduct - $cartPromotion['discount']), $shipping['totalShippingCost'], $carts);
        }

        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('thanh-toan', TRUE, TRUE),
        ];

        $system = $this->system;

        $config = $this->config();

        if(Agent::isMobile()){
            $template = 'mobile.cart.index';
        }else{
            $template = 'frontend.cart.index';
        }

        return view($template, compact(
            'config',
            'seo',
            'system',
            'provinces',
            'carts',
            'cartPromotion',
            'cartCaculate',
            'allVoucherTotal',
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

        if(Agent::isMobile()){
            $template = 'mobile.cart.pay';
        }else{
            $template = 'frontend.cart.pay';
        }

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
        $orders = $this->cartService->order($request, $system, $buyer);
        if($orders['flag']){
            return redirect()->route('cart.success')->with('success','Đặt hàng thành công');
        }
        return redirect()->route('cart.checkout')->with('error','Đặt hàng không thành công. Hãy thử lại');
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

        
        if(Agent::isMobile()){
            $template = 'mobile.cart.success';
        }else{
            $template = 'frontend.cart.success';
        }

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
