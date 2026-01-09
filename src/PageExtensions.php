<?php

namespace Tablo\Seo;

/**
 * Extended page methods for SEO functionality
 */
class PageExtensions
{
    /**
     * Generates an Open Graph image URL using the Tablo OG image service.
     *
     * @param \Kirby\Cms\Page $page The page instance
     * @param array $props Optional properties (title, author)
     * @return string The OG image URL
     */
    public static function ogImage($page, array $props = []): string
    {
        return OgImageGenerator::generate($page, $props);
    }
}
