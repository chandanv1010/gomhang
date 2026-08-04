<?php

namespace App\Services\V1\Product;

use App\Repositories\Product\PromotionRepository;
use App\Support\PromotionChain;
use Illuminate\Support\Collection;

/**
 * Single place that decides what a product costs once promotions are applied.
 *
 * Three code paths used to do this independently - ProductService for catalogue
 * and detail pages, WidgetService for the homepage, and the dashboard ajax
 * endpoint - which is why the same product could show a discount on one page and
 * full price on another. They all delegate here now.
 *
 * Each product gets two attributes:
 *   - promotions      the campaign in force right now, or null at full price
 *   - promotionChain  every upcoming segment in order, so a countdown can roll
 *                     on to the next campaign instead of stopping at zero
 */
class PromotionPricingService
{
    protected $promotionRepository;

    public function __construct(PromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * Attach promotion pricing to a list of products (paginator, collection or
     * plain array - anything traversable and index assignable).
     */
    public function attachToMany($products, array $productIds = [])
    {
        if (empty($productIds)) {
            $productIds = $this->idsOf($products);
        }

        if (empty($productIds)) {
            return $products;
        }

        $chains = $this->chainsFor($productIds);

        foreach ($products as $index => $product) {
            $chain = $chains[$product->id] ?? [];
            $products[$index]->promotionChain = $chain;
            $products[$index]->promotions = $this->currentOf($chain);
        }

        return $products;
    }

    /** Attach promotion pricing to a single product model. */
    public function attachToOne($product)
    {
        if (empty($product) || empty($product->id)) {
            return $product;
        }

        $chain = $this->chainsFor([$product->id])[$product->id] ?? [];
        $product->promotionChain = $chain;
        $product->promotions = $this->currentOf($chain);

        return $product;
    }

    /**
     * Promotion chains keyed by product id.
     *
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function chainsFor(array $productIds): array
    {
        $campaigns = $this->promotionRepository->findActiveAndUpcomingByProducts($productIds);

        if ($campaigns->isEmpty()) {
            return [];
        }

        $chains = [];
        foreach ($campaigns->groupBy('product_id') as $productId => $rows) {
            $chain = PromotionChain::build($rows);
            if (!empty($chain)) {
                $chains[(int) $productId] = $chain;
            }
        }

        return $chains;
    }

    /**
     * The segment covering right now, as an object so views and helpers can read
     * it the same way they read a model.
     */
    private function currentOf(array $chain)
    {
        $now = now();

        foreach ($chain as $segment) {
            $startsAt = \Carbon\Carbon::parse($segment['startsAt']);
            $endsAt = $segment['endsAt'] ? \Carbon\Carbon::parse($segment['endsAt']) : null;

            if ($startsAt->lessThanOrEqualTo($now) && ($endsAt === null || $endsAt->greaterThan($now))) {
                return (object) $segment;
            }
        }

        return null;
    }

    private function idsOf($products): array
    {
        if ($products instanceof Collection) {
            return $products->pluck('id')->filter()->values()->toArray();
        }

        $ids = [];
        foreach ($products as $product) {
            if (!empty($product->id)) {
                $ids[] = $product->id;
            }
        }

        return $ids;
    }
}
