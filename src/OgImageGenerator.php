<?php

namespace Tablo\Seo;

/**
 * Generates Open Graph image URLs
 */
class OgImageGenerator
{
    private const BASE_URL = 'https://og.tablo.me/card/%s.png';

    /**
     * Generates an Open Graph image URL using the Tablo OG image service.
     *
     * @param \Kirby\Cms\Page $page The page instance
     * @param array $props Optional properties (title, author)
     * @return string The OG image URL
     */
    public static function generate($page, array $props = []): string
    {
        // Build data array with provided props or page defaults
        $data = [
            't' => $props['title'] ?? $page->title()->value(),
            'a' => $props['author'] ?? $page->site()->title()->value(),
        ];

        // Encode as base64url (URL-safe base64)
        $encoded = strtr(rtrim(base64_encode(json_encode($data)), '='), [
            '+' => '-',
            '/' => '_',
        ]);

        return sprintf(self::BASE_URL, $encoded);
    }
}
