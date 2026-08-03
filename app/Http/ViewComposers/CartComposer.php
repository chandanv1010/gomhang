<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\V1\Core\CartService;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartComposer
{

    protected $cartService;

    public function __construct(
        CartService $cartService,
    ) {
        $this->cartService = $cartService;
    }

    public function compose(View $view)
    {

        $carts = Cart::instance('shopping')->content();
        $carts = $this->cartService->remakeCart($carts);
        $cartCaculate = $this->cartService->reCaculateCart();
        $wishlistCount = Cart::instance('wishlist')->count();
        $compareCount = Cart::instance('compare')->count();

        $view->with('cartShare', $cartCaculate);
        $view->with('wishlistCount', $wishlistCount);
        $view->with('compareCount', $compareCount);
    }
}
