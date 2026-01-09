<?php

namespace Tablo\Seo;

/**
 * Extended site methods for SEO functionality
 */
class SiteExtensions
{
    /**
     * Returns array of sitemap URLs for discovery.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array Array of sitemap URLs
     */
    public static function sitemaps($site): array
    {
        if (!option('tablo.seo.sitemapActive', true)) {
            return [];
        }
        return [$site->url() . '/sitemap.txt'];
    }
}
