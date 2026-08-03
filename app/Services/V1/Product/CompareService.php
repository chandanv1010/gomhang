<?php

namespace App\Services\V1\Product;

use App\Repositories\Product\ProductRepository;
use App\Services\V1\Product\ProductService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CompareService
{
    public const MAX_ITEMS = 4;

    protected ProductRepository $productRepository;
    protected ProductService $productService;

    public function __construct(ProductRepository $productRepository, ProductService $productService)
    {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }

    public function getPayload(int $languageId): array
    {
        $cartItems = Cart::instance('compare')->content();

        return [
            'compareSlots' => $this->buildSlots($cartItems, $languageId),
            'compareFields' => $this->fields(),
            'compareTotal' => $cartItems->count(),
        ];
    }

    public function renderTable(int $languageId): string
    {
        $payload = $this->getPayload($languageId);

        return view('frontend.product.catalogue.component.compare-table', [
            'compareSlots' => $payload['compareSlots'],
            'compareFields' => $payload['compareFields'],
        ])->render();
    }

    public function fields(): array
    {
        return [
            ['key' => 'price', 'label' => 'Giá bán', 'type' => 'html'],
            ['key' => 'code', 'label' => 'Mã sản phẩm'],
            ['key' => 'made_in', 'label' => 'Xuất xứ'],
            ['key' => 'stock', 'label' => 'Tồn kho'],
            ['key' => 'warranty', 'label' => 'Bảo hành'],
            // ['key' => 'seller', 'label' => 'Người phụ trách'],
            ['key' => 'rating', 'label' => 'Đánh giá'],
            ['key' => 'description', 'label' => 'Mô tả ngắn', 'type' => 'html'],
        ];
    }

    protected function buildSlots(Collection $cartItems, int $languageId): array
    {
        $slots = [];
        for ($i = 0; $i < self::MAX_ITEMS; $i++) {
            $slots[$i] = [
                'position' => $i,
                'rowId' => null,
                'product' => null,
            ];
        }

        $ids = $cartItems->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $products = collect();
        if (!empty($ids)) {
            $products = $this->productRepository->findByIds($ids, $languageId);
            $products = $this->productService->combineProductAndPromotion($ids, $products);
        }

        foreach ($cartItems as $item) {
            $position = (int) data_get($item->options, 'position', 0);
            if (!array_key_exists($position, $slots)) {
                continue;
            }

            $product = $products->firstWhere('id', (int) $item->id);
            if (!$product) {
                continue;
            }

            $slots[$position]['rowId'] = $item->rowId;
            $slots[$position]['product'] = $this->transformProduct($product);
        }

        return $slots;
    }

    protected function transformProduct($product): array
    {
        $price = getPrice($product);
        $review = getReview($product);
        $sellerName = optional($product->sellers)->name;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'canonical' => write_url($product->canonical),
            'image' => image($product->image),
            'price_html' => $price['html'],
            'fields' => [
                'price' => $price['html'],
                'code' => $product->code ?: '-',
                'made_in' => $product->made_in ?: '-',
                'stock' => !is_null($product->stock) ? number_format($product->stock) . ' sp' : '-',
                'warranty' => $product->warranty ?: '-',
                // 'seller' => $sellerName ?: '-',
                'rating' => $review['count'] > 0
                    ? $review['totalRate'] . '/5 (' . $review['count'] . ' đánh giá)'
                    : '-',
                'description' => '<p>' . e(Str::limit(strip_tags($product->description), 180)) . '</p>',
            ],
        ];
    }
}
