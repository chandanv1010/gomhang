<?php

namespace App\Services\V1\Core;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Services\V1\Product\ProductService;
use App\Services\V1\Product\PromotionService;


use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\PromotionRepository;
use App\Repositories\Core\OrderRepository;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Product\ProductVariantRepository;
use App\Repositories\Product\VoucherRepository;

use Gloudemans\Shoppingcart\Facades\Cart;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use App\Classes\ViettelPost;
use App\Enums\PromotionEnum;
use App\Enums\VoucherEnum;
use App\Models\Voucher;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isEmpty;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class CartService
{

    protected $productRepository;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $orderRepository;
    protected $voucherRepository;
    protected $productService;
    protected $promotionService;
    protected $customerRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,
        VoucherRepository $voucherRepository,
        ProductService $productService,
        PromotionService $promotionService,
        CustomerRepository $customerRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->orderRepository = $orderRepository;
        $this->voucherRepository = $voucherRepository;
        $this->productService = $productService;
        $this->promotionService = $promotionService;
        $this->customerRepository = $customerRepository;
    }

    public function handleApplyCartVoucher($voucherId)
    {
        if ($voucher = $this->handleVoucherCondition($voucherId)) {
            switch ($voucher->product_scope) {
                case VoucherEnum::ALL_PRODUCTS:
                    return $this->handleApplyVoucherForAllProduct($voucher);
                    break;
                case VoucherEnum::SPECIFIC_PRODUCTS:
                    return $this->handleApplyVoucherForSpecificProduct($voucher);
                    break;
                default:
                    return $this->handleApplyVoucherForCart($voucher);
                    break;
            }
        } else {
            throw new Exception('Voucher không hợp lệ');
        }
    }

    private function handleApplyVoucherForSpecificProduct($voucher)
    {
        if (!is_null(Session::get('voucher'))) {
            Session::forget('voucher');
        }
        $cartCaculate = $this->reCaculateCart();
        $productsByVoucher = $this->voucherRepository->getProductByVoucher($voucher->id)->pluck('id')->toArray();
        $carts = Cart::instance('shopping')->content();
        $productsId = $carts->pluck('id')->toArray();
        $combine_voucher = true;
        if ($productsItem = array_intersect($productsId, $productsByVoucher)) {
            foreach ($carts as $item) {
                $options = $item->options;
                $options['voucher'] = null;
                if (in_array($item->id, $productsItem)) {
                    if ($item->price < $item->priceOriginal) {
                        if ($voucher['combine_promotion'] !== VoucherEnum::COMBINE_PROMOTION) {
                            $combine_voucher = false;
                            continue;
                        }
                    }
                    $combine_voucher = true;
                    $this->hanleApplyVoucherForItem($item, $voucher);
                }
            }
        } else {
            throw new Exception('Voucher này không thể áp dụng vì không có sản phẩm phù hợp trong giỏ hàng');
        }
        if ($combine_voucher == false) {
            $cartCaculate['combine_voucher'] = false;
            return $cartCaculate;
        }
        $cartCaculate = $this->handleCartCaculate($carts, $voucher, $cartCaculate);
        return $cartCaculate;
    }

    private function handleApplyVoucherForAllProduct($voucher)
    {
        if (!is_null(Session::get('voucher'))) {
            Session::forget('voucher');
        }
        $carts = Cart::instance('shopping')->content();
        $cartCaculate = $this->reCaculateCart();
        $combine_voucher = true;
        foreach ($carts as $item) {
            if ($item->price < $item->priceOriginal) {
                if ($voucher['combine_promotion'] !== VoucherEnum::COMBINE_PROMOTION) {
                    $combine_voucher = false;
                    continue;
                }
            }
            $combine_voucher = true;
            $this->hanleApplyVoucherForItem($item, $voucher);
        }
        if ($combine_voucher == false) {
            $cartCaculate['combine_voucher'] = false;
            return $cartCaculate;
        }
        $cartCaculate = $this->handleCartCaculate($carts, $voucher, $cartCaculate);
        return $cartCaculate;
    }

    private function hanleApplyVoucherForItem($item, $voucher)
    {
        $product_price = $item->price;
        $options = $item->options->toArray();
        $voucher_product = ($product_price * $voucher['discount_value'] / 100);
        $discount = ($voucher['discount_type'] == VoucherEnum::FIXED)
            ? min($voucher['discount_value'], $voucher['max_discount_amount'])
            : $discount = min($voucher_product, $voucher['max_discount_amount']);
        $options['voucher'] = [
            'id' => $voucher['id'],
            'product_scope' => $voucher['product_scope'],
            'discount' => $discount,
            'combine_promotion' => $voucher['combine_promotion'],
            'status_combine' => true,
        ];
        Cart::instance('shopping')->update($item->rowId, ['options' => $options]);
    }

    private function handleCartCaculate($carts, $voucher, $cartCaculate)
    {
        session(['voucher' => $voucher]);
        $carts = $this->remakeCart($carts);
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $totalVoucherProduct = $this->totalDiscountVoucher($carts);
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $cartPromotion['discount'] - $totalVoucherProduct;
        $cartCaculate['voucherDiscount'] = $totalVoucherProduct;
        $cartCaculate['cartDiscount'] = $cartPromotion['discount'];
        $cartCaculate['voucher']['product_scope'] = $voucher->product_scope;
        return $cartCaculate;
    }

    private function handleApplyVoucherForCart($voucher)
    {

        $cartCaculate = $this->reCaculateCart($voucher);

        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal'], $voucher);

        $cartCaculate['cartDiscount']  = $cartPromotion['discount'];

        if ($cartPromotion['combine_voucher'] == false) {
            $cartCaculate['combine_voucher'] = false;
            return $cartCaculate;
        }

        $buyer = $this->getBuyer();

        $shipping = $this->totalShipping($buyer);

        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $cartPromotion['discount'];

        $cartCaculate['ship'] = !is_null($shipping) ? $shipping['totalShippingCost'] : 0;

        $cartCaculate['valid'] = true;

        if ($voucher->product_scope == VoucherEnum::SHIPPING_ORDERS) {
            $cartCaculate = $this->handleApplyVoucherShip($voucher, $cartCaculate);
        } else {
            $cartCaculate = $this->handleApplyCartVoucherTotal($voucher, $cartCaculate);
        }

        if ($cartCaculate['valid'] == false) {
            return $cartCaculate;
        }

        $carts = Cart::instance('shopping')->content();

        if ($carts) {
            foreach ($carts as $item) {
                $options = $item->options;
                $options['voucher'] = null;
                Cart::instance('shopping')->update($item->rowId, ['options' => $options]);
            }
        }

        $cartCaculate['combine_voucher'] = true;

        return $cartCaculate;
    }

    private function handleApplyVoucherShip($voucher, $cartCaculate)
    {
        $ship = $cartCaculate['ship'];
        if ($ship == 0) {
            $cartCaculate['valid'] = false;
            return $cartCaculate;
        }
        $shipApplyVoucher = $this->voucherRepository->getDiscountVoucherForCart($ship, $voucher->id);
        $cartCaculate['voucher']['discount'] = $shipApplyVoucher['discount'];
        $cartCaculate['ship'] =  $ship - $shipApplyVoucher['discount'];
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] + $cartCaculate['ship'];
        $this->handleSesion($cartCaculate);
        return $cartCaculate;
    }

    private function handleApplyCartVoucherTotal($voucher, $cartCaculate)
    {
        $discountVoucher = $this->voucherRepository->getDiscountVoucherForCart($cartCaculate['cartTotal'], $voucher->id);
        $total = $cartCaculate['cartTotal'] - $cartCaculate['ship'];
        if ($total < $discountVoucher['min_order_value']) {
            $cartCaculate['valid'] = false;
            $cartCaculate['min_order_value'] = $discountVoucher['min_order_value'];
            return $cartCaculate;
        }
        $cartCaculate['voucher']['discount'] = $discountVoucher['discount'];
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $discountVoucher['discount'] + $cartCaculate['ship'];
        $this->handleSesion($cartCaculate);
        return $cartCaculate;
    }

    private function handleSesion($cartCaculate)
    {
        $voucherOld = Session::get('voucher');
        if (!is_null($voucherOld)) {
            $voucherOld = Session::forget('voucher');
        }
        session(['voucher' => $cartCaculate['voucher']]);
    }

    private function handleVoucherCondition($voucherId)
    {

        return $this->voucherRepository->findById($voucherId);
    }

    private function getDiscountVoucherForProduct($productId)
    {
        return $this->voucherRepository->getVoucherForProduct($productId)->first()->discount;
    }

    private function handleVoucher($voucherId, $productId)
    {
        if ($voucher = $this->handleVoucherCondition($voucherId)) {
            if ($voucher['product_scope'] == VoucherEnum::ALL_PRODUCTS) {
                if ($this->productRepository->findById($productId)) {
                    $voucher['discount'] = $this->getDiscountVoucherForProduct($productId);
                    return $voucher;
                } else {
                    throw new Exception('Voucher không hợp lệ');
                }
            } else if ($voucher['product_scope'] == VoucherEnum::SPECIFIC_PRODUCTS) {
                $productVoucherIDs = $voucher->voucher_products->pluck('id')->toArray();
                if (in_array($productId, $productVoucherIDs)) {
                    $voucher['discount'] = $this->getDiscountVoucherForProduct($productId);
                    return $voucher;
                } else {
                    throw new Exception('Voucher không hợp lệ');
                }
            } else {
                throw new Exception('Voucher ko hợp lệ');
            }
        }
    }

    private function handleCombinePromotionGifts($product, $data, $payload)
    {
        $carts = Cart::instance('shopping')->content();
        $data_promotion = [];
        foreach ($product->promotion_gifts as $item) {
            if ($item['discount_information']['info']['model'] == $payload['type_promotion']) {
                $data_promotion = $item;
            }
        }
        $type = $data_promotion['discount_information']['info']['model'];
        switch ($type) {
            case PromotionEnum::SINGLE_PRODUCT:
                return $this->hanldePromitonForSingleProduct($data, $carts, $data_promotion);
            case PromotionEnum::MULTIPLE_PRODUCT:
                return $this->hanldePromitonForMultipleProduct($data, $carts, $data_promotion);
            default:
                break;
        }
    }

    private function hanldePromitonForSingleProduct($data, $carts, $data_promotion)
    {
        $dataGifts = [];
        $itemOnCarts = [];
        $productIdPromotion = $data_promotion['products'][0]['id'];
        $oldRate = 0;
        if ($carts->count('id') == 0) {
            $itemOnCarts[] = [
                'id' => $data['id'],
                'quantity' => $data['qty'],
                'gift' => ($data['id'] == $productIdPromotion) ? true : false,
                'price' => $data['price'],
                'old_quantity' => 0
            ];
        } else {
            foreach ($carts as $k => $v) {
                $itemOnCarts[] = [
                    'id' => $v->id,
                    'quantity' => $v->qty,
                    'gift' => false,
                    'price' => $v->price,
                    'old_quantity' => $v->qty
                ];
            }
            $found = false;
            foreach ($itemOnCarts as &$item) {
                if ($item['id'] == $data['id']) {
                    $item['old_quantity'] = (int)$item['quantity'];
                    $item['quantity'] += $data['qty'];
                    $item['gift'] = ($item['id'] == $productIdPromotion) ? true : false;
                    $item['price'] = $data['price'];
                    $found = true;
                    break;
                }
            }
            unset($item);
            if (!$found) {
                $itemOnCarts[] = [
                    'id' => $data['id'],
                    'quantity' => $data['qty'],
                    'gift' => ($data['id'] == $productIdPromotion) ? true : false,
                    'price' => $data['price'],
                    'old_quantity' => 0
                ];
            }
        }
        foreach ($itemOnCarts as $keyAttr => $valAttr) {
            if ($valAttr['gift'] == false) {
                continue;
            }
            $oldRate = floor($valAttr['old_quantity'] / $data_promotion['products'][0]['quantity']);
            $rate = floor($valAttr['quantity'] / $data_promotion['products'][0]['quantity']);
            $qty = $rate - $oldRate;
            $flag = ($qty > 0) ? true : false;
            if ($flag == false) {
                return;
            }
            foreach ($data_promotion['product_gifts'] as $k => $v) {
                $dataGifts[] = [
                    'id' => $v['id'],
                    'name' => $v['name'],
                    'qty' => $v['quantity'] * $qty,
                    'price' => 0,
                    'gift' => 'active'
                ];
            }
        }
        return $dataGifts;
    }

    private function hanldePromitonForMultipleProduct($data, $carts, $data_promotion)
    {
        $itemOnCarts = [];
        $dataGifts = [];
        if ($carts->count('id') == 0) {
            return;
        }
        foreach ($carts as $item) {
            $itemOnCarts[] = [
                'id' => $item->id,
                'quantity' => $item->qty,
                'price' => $item->price
            ];
        }
        foreach ($itemOnCarts as $it) {
            if ($it['id'] == $data['id']) {
                $it['quantity'] += $data['qty'];
            } else {
                $itemOnCarts[] = [
                    'id' => $data['id'],
                    'quantity' => $data['qty'],
                    'price' => $data['price']
                ];
            }
        }
        $conditionTakePromotion = $data_promotion['products'];
        $minRatio = PHP_INT_MAX;
        $countCondition = 0;
        $countItem = 0;
        foreach ($conditionTakePromotion as $condition) {
            $countCondition += $condition['quantity'];
            $found = false;
            foreach ($itemOnCarts as $item) {
                if ($item['id'] == $condition['id']) {
                    $countItem += $item['quantity'];
                    $ratio = floor($item['quantity'] / $condition['quantity']);
                    $minRatio = min($minRatio, $ratio);
                    $found = true;
                    break;
                }
            }
        }
        if (!$found) {
            return;
        }
        foreach ($data_promotion['product_gifts'] as $k => $v) {
            $dataGifts[] = [
                'id' => $v['id'],
                'name' => $v['name'],
                'qty' => $v['quantity'] * $minRatio,
                'price' => 0,
                'gift' => 'active'
            ];
        }

        return $dataGifts;
    }

    public function create($request, $language = 1)
    {
        try {
            $payload = $request->input();
            $voucherId = $request->input('voucher_id');
            if (!is_null(Session::get('voucher')) && !is_null($voucherId)) {
                Session::forget('voucher');
            }
            $this->handleCheckoutVoucherForCart($voucherId);
            $product = $this->productRepository->findById($payload['id'], ['*'], [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id',  $language);
                }
            ]);
            $data = [
                'id' => $product->id,
                'name' => $product->languages->first()->pivot->name,
                'qty' => $payload['quantity'],
            ];
            $dataGifts = null;
            if (isset($payload['attribute_id']) && count($payload['attribute_id'])) {
                $attributeId = sortAttributeId($payload['attribute_id']);
                $variant = $this->productVariantRepository->findVariant($attributeId, $product->id, $language);
                $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
                $variantPrice = getVariantPrice($variant, $variantPromotion);
                $data['id'] =  $product->id . '_' . $variant->uuid;
                $data['name'] = $product->languages->first()->pivot->name . ' ' . $variant->languages()->first()->pivot->name;
                $data['price'] = $variantPrice['hasPromotion'] ? $variantPrice['priceSale'] : $variantPrice['price'];
                $data['options'] = [
                    'attribute' => $payload['attribute_id'],
                ];
            } else {
                $product = $this->productService->combineProductAndPromotion([$product->id], $product, true);
                $price = getPrice($product);
                // Read hasPromotion rather than testing priceSale > 0, so a
                // campaign that discounts to exactly 0 is still honoured.
                $data['price'] = $price['hasPromotion'] ? $price['priceSale'] : $price['price'];
                if (isset($payload['type_promotion'])) {
                    $promotion_gifts = $this->promotionService->getProTakeGiftBuyProduct($product->id);
                    $flag = false;
                    foreach ($promotion_gifts as $item) {
                        if ($item['discount_information']['info']['model'] == $payload['type_promotion']) {
                            $flag = true;
                            break;
                        }
                    }
                    if (!$flag) {
                        throw new Exception('Khuyến mại không hợp lệ !');
                    }
                    $product['promotion_gifts'] = $promotion_gifts;
                    $dataGifts = $this->handleCombinePromotionGifts($product, $data, $payload);
                }
                $data['options'] = [
                    'voucher' => (!is_null($voucherId)) ? $this->handleVoucher($voucherId, $product->id) : null
                ];
            }
            Cart::instance('shopping')->add($data);
            if (!is_null($dataGifts)) {
                foreach ($dataGifts as $item) {
                    $item['options'] = [
                        'gifts' => 'active'
                    ];
                    Cart::instance('shopping')->add($item);
                }
            }
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
            die();
            return false;
        }
    }

    public function update($request)
    {
        try {
            $payload = $request->input();
            Cart::instance('shopping')->update($payload['rowId'], $payload['qty']);
            
            $cartItem = Cart::instance('shopping')->get($payload['rowId']);
            $cartCaculate = $this->cartAndPromotion();
            $cartCaculate['cartItemUpdate'] = $cartItem;
            $cartCaculate['cartItemSubTotal'] = $cartItem->qty * $cartItem->price - ($cartItem->options->voucher->discount ?? 0);
            return $cartCaculate;
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
            die();
            return false;
        }
    }

    public function delete($request)
    {
        try {
            $payload = $request->input();
            $voucher = Session::get('voucher');
            $cartItemRemove = Cart::instance('shopping')->get($payload['rowId']);
            $voucherUnActive = null;
            if (!is_null($cartItemRemove->options->voucher)) {
                $voucherUnActive = $this->handleStatusVoucher($cartItemRemove);
            }
            Cart::instance('shopping')->remove($payload['rowId']);
            
            $cartCaculate = $this->cartAndPromotion();
            if ($cartCaculate['cartTotal'] == 0) {
                if (!is_null($voucher)) {
                    Session::forget('voucher');
                }
            }
            $cartCaculate['voucherUnActive'] = $voucherUnActive;
            return $cartCaculate;
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
            die();
            return false;
        }
    }

    public function order($request, $system, $buyer = null)
    {
        DB::beginTransaction();
        try {
            $carts = Cart::instance('shopping')->content();
            $orderSeparate = $this->orderSeparate();
            $orders = [];
            $totalAmount = 0;
            $totalItems = 0;
            $orderIds = [];
            if ($orderSeparate) {
                foreach ($orderSeparate as $keySellerOrder => $sellerOrder) {
                    //Tính toán lại tổng tiền cho mỗi đơn hàng tách ra
                    $cartCaculateForSeller = $this->calculateCartForSeller($sellerOrder['items']);
                    $cartPromotion = $this->cartPromotion($cartCaculateForSeller['cartTotal']);
                    $payload = $this->request($request, $cartCaculateForSeller, $cartPromotion);
                    $payload['point_used'] = $request->input('point_redeem', 0);
                    // Thêm vào thông tin người mua
                    if (!is_null($buyer)) {
                        $payload['customer_id'] = $buyer->id;
                    }
                    $shipping = $this->totalShipping($buyer);
                    $payload['cart']['cartVoucher'] = $this->handleTotalVoucher($carts);
                    $payload['shipping'] = $shipping['sellerShippingCost'][$sellerOrder['seller_id']]['cost'] ?? 0;
                    $order = $this->orderRepository->create($payload, ['products']);
                    if ($order->id > 0) {
                        $this->createOrderProduct($payload, $order, $sellerOrder, $request);
                        if ($payload['cart']['cartVoucher'] !== 0) {
                            $this->createOrderVoucher($order, $carts);
                        }
                        $orders[] = $order;
                        $totalAmount += $order['cart']['cartTotal'];
                        $totalItems += $order['cart']['cartTotalItems'];
                        $orderIds[] = $order->id;
                    }
                }
            }
            $orderSummary = [
                'orders' => $orders,
                'totalAmount' => $totalAmount,
                'totalItems' => $totalItems,
                'orderIds' => $orderIds,
                'buyer' => $buyer,
                'orderCount' => count($orders),
                'flag' => TRUE,
            ];
            session(['orderSummary' => $orderSummary]);
            Cart::instance('shopping')->destroy();
            DB::commit();
            return $orderSummary;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return [
                'order' => null,
                'flag' => false,
            ];
        }
    }

    public function pay($request, $system, $buyer = null)
    {
        DB::beginTransaction();
        try {
            $carts = Cart::instance('pay')->content();
            $orderSeparate = $this->orderSeparate();
            $orders = [];
            $totalAmount = 0;
            $totalItems = 0;
            $orderIds = [];
            if ($orderSeparate) {
                foreach ($orderSeparate as $keySellerOrder => $sellerOrder) {
                    //Tính toán lại tổng tiền cho mỗi đơn hàng tách ra
                    $cartCaculateForSeller = $this->calculateCartForSeller($sellerOrder['items']);
                    $cartPromotion = $this->cartPromotion($cartCaculateForSeller['cartTotal']);
                    $payload = $this->request($request, $cartCaculateForSeller, $cartPromotion);
                    // Thêm vào thông tin người mua
                    if (!is_null($buyer)) {
                        $payload['customer_id'] = $buyer->id;
                    }
                    $shipping = $this->totalShipping($buyer);
                    $payload['cart']['cartVoucher'] = $this->handleTotalVoucher($carts);
                    $payload['shipping'] = $shipping['sellerShippingCost'][$sellerOrder['seller_id']]['cost'] ?? 0;
                    $order = $this->orderRepository->create($payload, ['products']);
                    if ($order->id > 0) {
                        $this->createOrderProduct($payload, $order, $sellerOrder, $request);
                        if ($payload['cart']['cartVoucher'] !== 0) {
                            $this->createOrderVoucher($order, $carts);
                        }
                        $orders[] = $order;
                        $totalAmount += $order['cart']['cartTotal'];
                        $totalItems += $order['cart']['cartTotalItems'];
                        $orderIds[] = $order->id;
                    }
                }
            }
            $orderSummary = [
                'orders' => $orders,
                'totalAmount' => $totalAmount,
                'totalItems' => $totalItems,
                'orderIds' => $orderIds,
                'buyer' => $buyer,
                'orderCount' => count($orders),
                'flag' => TRUE,
            ];
            session(['orderSummary' => $orderSummary]);
            Cart::instance('shopping')->destroy();
            DB::commit();
            return $orderSummary;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return [
                'order' => null,
                'flag' => false,
            ];
        }
    }

    private function createOrderVoucher($order, $carts)
    {
        $customer = Auth::guard('customer')->user();
        $voucher = Session::get('voucher');
        $qtyVoucherUse = VoucherEnum::QUANTITY;
        if (is_null($voucher)) {
            foreach ($carts as $item) {
                $options = $item->options;
                if (!is_null($options['voucher'])) {
                    $voucher = $options['voucher']->toArray();
                    break;
                }
            }
        } else {
            Session::forget('voucher');
        }
        $voucher_id = $voucher['id'];
        $voucher = $this->voucherRepository->findById($voucher_id);
        $payload = [
            'total_quantity' => $voucher['total_quantity'] - $qtyVoucherUse,
            'used_quantity' => $qtyVoucherUse
        ];
        $this->voucherRepository->update($voucher->id, $payload);
        $this->createPivotVoucherUsage($order, $voucher, $customer);
        $this->createPivotVoucherOrder($order, $voucher, $customer);
    }

    private function createPivotVoucherUsage($order, $voucher, $customer)
    {
        $payload = [
            'order_id' => $order->id,
            'voucher_id' => $voucher->id,
            'customer_id' => $customer->id,
        ];
        $this->orderRepository->createPivot($order, $payload, 'voucher_usages');
    }

    private function createPivotVoucherOrder($order, $voucher, $customer)
    {
        $payload = [
            'order_id' => $order->id,
            'voucher_id' => $voucher->id,
        ];
        $this->orderRepository->createPivot($order, $payload, 'voucher_orders');
    }

    private function handleTotalVoucher($carts)
    {
        $discount = 0;
        $voucher = Session::get('voucher');
        if (is_null($voucher)) {
            foreach ($carts as $item) {
                $options = $item->options;
                if (!is_null($options['voucher'])) {
                    $discount += $options['voucher']['discount'];
                }
            }
            return $discount;
        }
        if ($voucher['product_scope'] == VoucherEnum::TOTAL_ORDERS || $voucher['product_scope'] == VoucherEnum::SHIPPING_ORDERS) {
            $discount = $voucher['discount'];
        } else {
            foreach ($carts as $item) {
                $options = $item->options;
                if (!is_null($options['voucher'])) {
                    $discount += $options['voucher']['discount'] * $item->qty;
                }
            }
        }
        return $discount;
    }

    public function totalShipping($buyer = null)
    {
        $orderSeparate = $this->orderSeparate();
        $shippingCosts = [];
        $productsHasCost = [];
        $totalShippingCost = 0;
        if (isset($orderSeparate) &&  count($orderSeparate)) {
            foreach ($orderSeparate as $key => $val) {
                if ($key == 'null') continue; // Loại bỏ những sản phẩm ko có seller_id

                $cartCaculateForSeller = $this->calculateCartForSeller($val['items']);

                $seller = $this->customerRepository->findById($key);

                $totalPrice = $cartCaculateForSeller['cartTotal'];

                $totalWeight = 0;

                foreach ($val['items'] as $item) {
                    $productsHasCost[] = [
                        'id' => $item->id
                    ];
                    $product = $this->productRepository->findById($item->id);
                    //Nếu ko có khối lượng của sản phẩm thì gán mặc định sản phẩm nặng 100g
                    $totalWeight += ($product->weight ?? 100) * $item->qty;
                }

                $viettelPost = new ViettelPost(
                    $seller->viettelpost_email,
                    $seller->viettelpost_password
                );

                $token = $viettelPost->getToken();
                $shippingCost = $viettelPost->shipping($buyer, $seller, $totalWeight, $totalPrice, $token);
                $totalShippingCost += $shippingCost;
                $shippingCosts[$seller->id] = [
                    'cost' => $shippingCost,
                    'weight' => $totalWeight,
                    'totalPrice' => $totalPrice,
                ];
            }

            return [
                'totalShippingCost' => $totalShippingCost,
                'sellerShippingCost' => $shippingCosts,
                'productsHasCost' => $productsHasCost
            ];
        }
    }

    private function orderSeparate()
    {
        $carts = Cart::instance('shopping')->content();
        $productsId = $carts->pluck('id');
        $products = $this->productRepository->findByIds($productsId, 1)->keyBy('id');
        $groupedItems = $carts->groupBy(function ($item) use ($products) {
            $sellerId = $products->get($item->id)->seller_id ?? null;
            return $sellerId ?: 'null';
        });

        $result = $groupedItems->map(function ($items, $sellerId) {
            return [
                'seller_id' => $sellerId === 'null' ? null : (int)$sellerId,
                'items' => $items,
                'total' => $items->sum(function ($item) {
                    return $item->price * $item->qty;
                })
            ];
        });
        return $result;
    }

    private function calculateCartForSeller($sellerItems)
    {
        $total = 0;
        $totalItems = 0;
        foreach ($sellerItems as $item) {
            $total += $item->price * $item->qty;
            $totalItems += $item->qty;
        }

        return [
            'cartTotal' => $total,
            'cartTotalItems' => $totalItems
        ];
    }

    private function mail($order, $sytem)
    {
        $to = $order->email;
        $cc = $sytem['contact_email'];
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $cartCaculate = $this->cartAndPromotion();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $data = [
            'order' => $order,
            'carts' => $carts,
            'cartCaculate' => $cartCaculate,
            'cartPromotion' => $cartPromotion
        ];

        Mail::to($to)->cc($cc)->send(new OrderMail($data));
    }

    private function createOrderProduct($payload, $order, $sellerOrder, $request)
    {
        // Tách cart theo seller id
        $orderSeparate = $this->orderSeparate();

        //Sau đó lọc ra các seller id trong phiên tính toán hiện tại
        $sellerCart = collect($orderSeparate)->first(function ($sellOrder) use ($sellerOrder) {
            return $sellOrder['seller_id'] == $sellerOrder['seller_id'];
        });


        if (!is_null($sellerCart['items'])) {
            foreach ($sellerCart['items'] as $key => $val) {
                $extract = explode('_', $val->id);
                $temp[] = [
                    'product_id' => $extract[0],
                    'uuid' => ($extract[1]) ?? null,
                    'name' => $val->name,
                    'qty' => $val->qty,
                    'price' => $val->price,
                    'priceOriginal' => $val->priceOriginal,
                    'option' => json_encode($val->options),
                ];
            }
        }

        $order->products()->sync($temp);
    }

    private function request($request, $cartCaculate, $cartPromotion)
    {
        $payload = $request->except(['_token', 'voucher', 'create']);
        $payload['code'] = time();
        $payload['cart'] = $cartCaculate;
        $payload['promotion']['discount'] = $cartPromotion['discount'] ?? '';
        $payload['promotion']['name'] = $cartPromotion['selectedPromotion']->name ?? '';
        $payload['promotion']['code'] = $cartPromotion['selectedPromotion']->code ?? '';
        $payload['promotion']['startDate'] = $cartPromotion['selectedPromotion']->startDate ?? '';
        $payload['promotion']['endDate'] = $cartPromotion['selectedPromotion']->endDate ?? '';
        $payload['confirm'] = 'pending';
        $payload['delivery'] = 'pending';
        $payload['payment'] = 'unpaid';
        return $payload;
    }

    private function handleCartVoucher($cartCaculate)
    {
        // // $discount = 0;
        // // $qtyVoucher = 0;
        // // $payload = [];
        // // $carts = Cart::instance('shopping')->content();
        // // if(is_null(Session::get('voucher'))){
        // //     return $discount;
        // // }
        // // $discount = $this->totalDiscountVoucher($carts);
        // // foreach($carts as $item){
        // //     $options = $item->options->toArray();
        // //     if(!is_null($options['voucher'])){
        // //         $qtyVoucher += $item->qty;
        // //         $payload = $options['voucher'];
        // //     }
        // // }
        // $discount = 0;
        // $discount = $this->totalDiscountVoucher($carts);
        // return $discount;
    }

    // private function shipping(){
    //     $carts = Cart::instance('shopping')->content();
    //     $totalShipping = 0;
    //     foreach ($carts as $cartItem) {
    //         $option = $cartItem->options;
    //         if(isset($option['shipping'])){
    //             $totalShipping = $totalShipping + $option['shipping'];
    //         }
    //     }
    //     return $totalShipping;
    // }

    private function cartAndPromotion()
    {
        $buyer = $this->getBuyer();
        $shipping = $this->totalShipping($buyer);
        $carts = Cart::instance('shopping')->content();
        $cartVoucher = $this->totalDiscountVoucher($carts);
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $cartCaculate['cartVoucher'] = $cartVoucher;
        $cartCaculate['ship'] = !is_null($shipping) ? $shipping['totalShippingCost'] : 0;
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $cartPromotion['discount'] - $cartVoucher + $cartCaculate['ship'];
        $cartCaculate['cartDiscount'] = $cartPromotion['discount'];
        return $cartCaculate;
    }

    public function reCaculateCart($voucher = null)
    {
        $carts = Cart::instance('shopping')->content();
        $total = 0;
        $totalItems = 0;
        foreach ($carts as $cart) {
            $total = $total + $cart->price * $cart->qty;
            $totalItems = $totalItems + $cart->qty;
        }
        if (!is_null($voucher)) {
            $voucher = [
                'id' => $voucher['id'],
                'discount' => $voucher['discount'],
                'product_scope' => $voucher['product_scope'],
                'min_value' => $voucher['min_value'],
                'combine_promotion' => $voucher['combine_promotion'],
                'status_active' => true
            ];
        }
        return [
            'cartTotal' => $total,
            'cartTotalItems' => $totalItems,
            'voucher' => $voucher
        ];
    }

    public function remakeCart($carts)
    {

        $buyer = Auth::guard('customer')->user();

        $cartId = $carts->pluck('id')->all();

        $temp = [];

        $objects = [];

        if (count($cartId)) {
            foreach ($cartId as $key => $val) {
                $extract = explode('_', $val);
                if (count($extract) > 1) {
                    $temp['variant'][] = $extract[1];
                } else {
                    $temp['product'][] = $extract[0];
                }
            }

            if (isset($temp['variant'])) {
                $objects['variants'] = $this->productVariantRepository->findByCondition(
                    [],
                    true,
                    [],
                    ['id', 'desc'],
                    ['whereIn' => $temp['variant'], 'whereInField' => 'uuid']
                )->keyBy('uuid');
            }

            if (isset($temp['product'])) {
                $objects['products'] = $this->productRepository->findByCondition(
                    [
                        config('apps.general.defaultPublish')
                    ],
                    true,
                    [],
                    ['id', 'desc'],
                    ['whereIn' => $temp['product'], 'whereInField' => 'id']
                )->keyBy('id');
            }
            foreach ($carts as $keyCart => $cart) {
                $explode = explode('_', $cart->id);
                $objectId = $explode[1] ?? $explode[0];
                $voucher = $cart->options['voucher'] ?? null;
                if (isset($objects['variants'][$objectId])) {
                    $variantItem = $objects['variants'][$objectId];
                    $variantImage = explode(',', $variantItem->album)[0] ?? null;
                    $cart->image = $variantImage;
                    $cart->priceOriginal = $variantItem->price;
                } elseif (isset($objects['products'][$objectId])) {
                    $productItem = $objects['products'][$objectId];
                    $cart->image = $productItem->image;
                    $cart->priceOriginal = $productItem->price;
                    if (!is_null($voucher)) {
                        $onPromotion = ($cart->priceOriginal > $cart->priceTax) ? true : false;
                        if ($onPromotion == true) {
                            $voucher['status_combine'] = ($voucher['combine_promotion'] == VoucherEnum::COMBINE_PROMOTION) ? true : false;
                        } else {
                            $voucher['status_combine'] = true;
                        }
                        $cart->options['voucher'] = $voucher;
                    }
                }
                // $cart->options->put('shipping', $this->caculateShipFee($buyer, $cart));
            }
        }
        return $carts;
    }

    public function cartPromotion($cartTotal = 0, $voucher = null)
    {
        $maxDiscount = 0;
        $selectedPromotion = null;
        $promotions = $this->promotionRepository->getPromotionByCartTotal();
        $combine_voucher = true;
        if (!is_null($promotions)) {
            foreach ($promotions as $promotion) {
                $discount = $promotion->discountInformation['info'];
                $amountFrom = $discount['amountFrom'] ?? [];
                $amountTo = $discount['amountTo'] ?? [];
                $amountValue = $discount['amountValue'] ?? [];
                $amountType = $discount['amountType'] ?? [];
                if (!empty($amountFrom) && count($amountFrom) == count($amountTo) && count($amountTo) == count($amountValue)) {
                    for ($i = 0; $i < count($amountFrom); $i++) {
                        $currentAmountFrom = convert_price($amountFrom[$i]);
                        $currentAmountTo = convert_price($amountTo[$i]);
                        $currentAmountValue = convert_price($amountValue[$i]);
                        $currentAmountType = $amountType[$i];
                        if ($cartTotal > $currentAmountFrom && ($cartTotal <= $currentAmountTo) || $cartTotal > $currentAmountTo) {
                            if ($currentAmountType == 'cash') {
                                $maxDiscount = max($maxDiscount, $currentAmountValue);
                            } else if ($currentAmountType == 'percent') {
                                $discountValue = ($currentAmountValue / 100) * $cartTotal;
                                $maxDiscount = max($maxDiscount, $discountValue);
                            }
                            $selectedPromotion = $promotion;
                        }
                    }
                }
            }
            if (!is_null($voucher)) {
                if ($voucher['combine_promotion'] !== VoucherEnum::COMBINE_PROMOTION) {
                    $combine_voucher = false;
                }
            }
        }
        return [
            'discount' => $maxDiscount,
            'selectedPromotion' => $selectedPromotion,
            'combine_voucher' => $combine_voucher
        ];
    }

    public function totalDiscountVoucher($carts)
    {
        $total = 0;
        if ($carts) {
            foreach ($carts as $k => $v) {
                $voucher = $v->options->voucher ?? null;
                if (!is_null($voucher)) {
                    $total += ($voucher['status_combine'] == true) ? $voucher['discount'] * $v->qty : 0;
                }
            }
        }
        return $total;
    }

    private function handleCheckoutVoucherForCart($voucherId = null)
    {
        $cart = Cart::instance('shopping')->content();
        if (!is_null($voucherId) && !is_null($cart)) {
            foreach ($cart as $item) {
                $options = $item->options;
                if (!is_null($options)) {
                    $options['voucher'] = null;
                    Cart::instance('shopping')->update($item->rowId, ['options' => $options]);
                }
            }
        }
    }

    private function getBuyer()
    {
        return Auth::guard('customer')->user();
    }

    private function handleStatusVoucher($cartItemRemove)
    {
        $voucher = $cartItemRemove->options->voucher;
        $voucherUnActive = $voucher['id'];
        return $voucherUnActive;
    }

    public function handleUnUseVoucher($voucher_id)
    {
        $voucher = $this->handleVoucherCondition($voucher_id);
        if ($voucher) {
            $voucher_session = Session::get('voucher');
            if (!is_null($voucher_session)) {
                Session::forget('voucher');
            } else {
                $this->handleCheckoutVoucherForCart($voucher_id);
            }
        }
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $cartPromotion['discount'];
        return $cartCaculate;
    }
}
