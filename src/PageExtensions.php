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

    /**
     * Whether the page is no-index, either globally via
     * `tablo.seo.forceNoindex` or individually via `robots_noindex`.
     */
    public static function isNoindex(Page $page): bool
    {
        return option('tablo.seo.forceNoindex', false) || $page->robots_noindex()->toBool();
    }

    /**
     * Builds the `<meta name="robots">` content value from `robots_noindex`
     * / `robots_nofollow`, and `tablo.seo.forceNoindex`. Returns null when no
     * directive applies, so templates can skip rendering the tag entirely.
     */
    public static function metaRobots(Page $page): string|null
    {
        $directives = [];

        if (self::isNoindex($page)) {
            $directives[] = 'noindex';
            $directives[] = 'noarchive';
        }

        if ($page->robots_nofollow()->toBool()) {
            $directives[] = 'nofollow';
        }

        return $directives === [] ? null : implode(', ', $directives);
    }
}
