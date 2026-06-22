<?php

namespace Tablo\Seo;

use Kirby\Cms\File;
use Kirby\Cms\Page;

/**
 * Extended page methods for SEO functionality
 */
class PageExtensions
{
    /**
     * Returns the custom Open Graph image URL when selected, otherwise
     * generates one using the Tablo OG image service.
     *
     * @param Page $page The page instance
     * @param array $props Optional properties (title, author)
     * @return string The OG image URL
     */
    public static function ogImage(Page $page, array $props = []): string
    {
        if ($file = self::ogImageFile($page)) {
            return $file->url();
        }

        return OgImageGenerator::generate($page, $props);
    }

    /**
     * Returns the optional custom Open Graph image file.
     */
    public static function ogImageFile(Page $page): File|null
    {
        return $page->seo_og_image()->toFile();
    }
}
