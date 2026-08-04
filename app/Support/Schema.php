<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Builds JSON-LD structured data.
 *
 * The previous implementation concatenated JSON by hand with escaped quotes. Four
 * of the five page types emitted syntactically invalid JSON - two top level
 * objects separated by a comma with no array around them - so Google could not
 * parse any of it. Product also advertised a hardcoded brand from a different
 * company ("An Hưng"), an empty offers.price and an aggregateRating even with
 * zero reviews, each of which invalidates the Product rich result on its own.
 *
 * Everything here goes through json_encode, so the output cannot be malformed and
 * quotes or diacritics in product names are escaped correctly.
 */
class Schema
{
    /**
     * Wrap nodes in one @graph and render the script tag.
     *
     * Slashes stay escaped on purpose: it makes a literal "</script>" inside any
     * value impossible, which would otherwise end the script element early.
     *
     * @param  array<int, array<string, mixed>>  $nodes
     */
    public static function script(array $nodes): string
    {
        $nodes = array_values(array_filter($nodes));

        if (empty($nodes)) {
            return '';
        }

        $payload = [
            '@context' => 'https://schema.org',
            '@graph' => $nodes,
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return '<script type="application/ld+json">' . $json . '</script>';
    }

    /** @return array<string, mixed> */
    public static function organization(array $system = []): array
    {
        $name = system_brand($system);
        $logo = self::absolute($system['homepage_logo'] ?? '');
        $phone = system_phone($system);

        $node = [
            '@type' => 'Organization',
            '@id' => self::base() . '#organization',
            'name' => $name,
            'url' => self::base(),
        ];

        if ($logo !== '') {
            $node['logo'] = ['@type' => 'ImageObject', 'url' => $logo];
        }
        if ($phone !== '') {
            $node['contactPoint'] = [
                '@type' => 'ContactPoint',
                'telephone' => $phone,
                'contactType' => 'customer service',
                'areaServed' => 'VN',
                'availableLanguage' => ['Vietnamese'],
            ];
        }

        $social = array_values(array_filter([
            $system['homepage_intro_youtube'] ?? null,
            $system['homepage_intro_tiktok'] ?? null,
        ]));
        if (!empty($social)) {
            $node['sameAs'] = $social;
        }

        return $node;
    }

    /** @return array<string, mixed> */
    public static function webSite(array $system = []): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::base() . '#website',
            'name' => system_brand($system),
            'url' => self::base(),
            'inLanguage' => 'vi-VN',
            'publisher' => ['@id' => self::base() . '#organization'],
            // Lets Google offer a sitelinks search box.
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => rtrim(self::base(), '/') . '/tim-kiem?keyword={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * @param  array<int, array{name: string, url?: string}>  $trail
     * @return array<string, mixed>|null
     */
    public static function breadcrumb(array $trail): ?array
    {
        $items = [];
        $position = 1;

        foreach ($trail as $entry) {
            $name = trim((string) ($entry['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $item = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $name,
            ];
            // The last crumb is the current page and needs no item URL.
            if (!empty($entry['url'])) {
                $item['item'] = self::absolute($entry['url']);
            }
            $items[] = $item;
        }

        if (count($items) < 2) {
            return null; // A one-item breadcrumb is not worth emitting.
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * @param  array{name: string, url: string, image?: string, description?: string,
     *               sku?: string, brand?: string, category?: string, price: float,
     *               priceValidUntil?: string|null, inStock?: bool,
     *               ratingValue?: float|null, reviewCount?: int, reviews?: array}  $data
     * @return array<string, mixed>
     */
    public static function product(array $data, array $system = []): array
    {
        $url = self::absolute($data['url'] ?? '');

        $node = [
            '@type' => 'Product',
            'name' => trim((string) $data['name']),
            'url' => $url,
        ];

        if (!empty($data['description'])) {
            $node['description'] = self::text($data['description'], 500);
        }
        if (!empty($data['image'])) {
            $node['image'] = self::absolute($data['image']);
        }
        if (!empty($data['sku'])) {
            $node['sku'] = (string) $data['sku'];
        }
        if (!empty($data['brand'])) {
            $node['brand'] = ['@type' => 'Brand', 'name' => (string) $data['brand']];
        }
        if (!empty($data['category'])) {
            $node['category'] = (string) $data['category'];
        }

        // An Offer without a price is rejected, so only emit one when there is a
        // real price to state.
        $price = (float) ($data['price'] ?? 0);
        if ($price > 0) {
            $offer = [
                '@type' => 'Offer',
                'url' => $url,
                'price' => number_format($price, 0, '.', ''),
                'priceCurrency' => 'VND',
                'availability' => !empty($data['inStock'])
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
                'seller' => ['@id' => self::base() . '#organization'],
            ];

            if (!empty($data['priceValidUntil'])) {
                $offer['priceValidUntil'] = Carbon::parse($data['priceValidUntil'])->toDateString();
            }

            $node['offers'] = $offer;
        }

        // aggregateRating requires at least one review; emitting it with a count of
        // zero makes the whole Product invalid.
        $reviewCount = (int) ($data['reviewCount'] ?? 0);
        $ratingValue = $data['ratingValue'] ?? null;
        if ($reviewCount > 0 && $ratingValue > 0) {
            $node['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) round((float) $ratingValue, 1),
                'reviewCount' => $reviewCount,
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }

        if (!empty($data['reviews'])) {
            $node['review'] = array_values(array_filter(array_map(
                static function ($review) {
                    $score = (float) ($review['score'] ?? 0);
                    $author = trim((string) ($review['author'] ?? ''));
                    if ($score <= 0 || $author === '') {
                        return null;
                    }

                    $node = [
                        '@type' => 'Review',
                        'author' => ['@type' => 'Person', 'name' => $author],
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => (string) round($score, 1),
                            'bestRating' => '5',
                            'worstRating' => '1',
                        ],
                    ];
                    if (!empty($review['body'])) {
                        $node['reviewBody'] = self::text($review['body'], 300);
                    }
                    if (!empty($review['date'])) {
                        $node['datePublished'] = Carbon::parse($review['date'])->toDateString();
                    }

                    return $node;
                },
                $data['reviews']
            )));
        }

        return $node;
    }

    /**
     * A category listing: CollectionPage plus the products it shows.
     *
     * @param  array{name: string, url: string, description?: string}  $page
     * @param  array<int, array{name: string, url: string, image?: string, price?: float}>  $items
     * @return array<string, mixed>
     */
    public static function collectionPage(array $page, array $items): array
    {
        $listItems = [];
        $position = 1;

        foreach ($items as $item) {
            if (empty($item['name'])) {
                continue;
            }
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'url' => self::absolute($item['url'] ?? ''),
                'name' => trim((string) $item['name']),
            ];
        }

        $node = [
            '@type' => 'CollectionPage',
            'name' => trim((string) $page['name']),
            'url' => self::absolute($page['url'] ?? ''),
            'inLanguage' => 'vi-VN',
            'isPartOf' => ['@id' => self::base() . '#website'],
        ];

        if (!empty($page['description'])) {
            $node['description'] = self::text($page['description'], 500);
        }
        if (!empty($listItems)) {
            $node['mainEntity'] = [
                '@type' => 'ItemList',
                'numberOfItems' => count($listItems),
                'itemListElement' => $listItems,
            ];
        }

        return $node;
    }

    /**
     * @param  array{headline: string, url: string, image?: string, description?: string,
     *               datePublished?: string|null, dateModified?: string|null,
     *               section?: string}  $data
     * @return array<string, mixed>
     */
    public static function article(array $data, array $system = []): array
    {
        $node = [
            '@type' => 'Article',
            'headline' => self::text($data['headline'], 110),
            'url' => self::absolute($data['url'] ?? ''),
            'inLanguage' => 'vi-VN',
            'isPartOf' => ['@id' => self::base() . '#website'],
            'publisher' => ['@id' => self::base() . '#organization'],
            'author' => ['@id' => self::base() . '#organization'],
        ];

        if (!empty($data['description'])) {
            $node['description'] = self::text($data['description'], 500);
        }
        if (!empty($data['image'])) {
            $node['image'] = self::absolute($data['image']);
        }
        if (!empty($data['datePublished'])) {
            $node['datePublished'] = Carbon::parse($data['datePublished'])->toIso8601String();
        }
        if (!empty($data['dateModified'])) {
            $node['dateModified'] = Carbon::parse($data['dateModified'])->toIso8601String();
        }
        if (!empty($data['section'])) {
            $node['articleSection'] = (string) $data['section'];
        }

        return $node;
    }

    /* ------------------------------------------------------------- helpers */

    private static function base(): string
    {
        return rtrim((string) config('app.url'), '/') . '/';
    }

    /** Structured data requires absolute URLs. */
    public static function absolute(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        return self::base() . ltrim($url, '/');
    }

    /** Plain text, collapsed whitespace, trimmed to a sane length. */
    private static function text(?string $value, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)));

        return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit - 1) . '…' : $text;
    }
}
