<?php

namespace Tablo\Seo;

use Kirby\Http\Response;

/**
 * Generates sitemap.txt content
 */
class SitemapGenerator
{
    /**
     * Generates sitemap.txt response with all listed pages.
     *
     * @return \Kirby\Http\Response The sitemap.txt response
     */
    public static function generate(): Response
    {
        $pages = [];

        // Add homepage if not listed
        if (!site()->homePage()->isListed()) {
            $pages[] = site()->url();
        }

        // Iterate over all listed pages
        foreach (site()->index()->listed() as $page) {
            // Check if all parents are listed
            foreach ($page->parents() as $parent) {
                if (!$parent->isListed()) {
                    continue 2;
                }
            }
            $pages[] = $page->url();
        }

        // Filter unique pages and return sitemap
        $response = new Response(join("\n", array_unique($pages)), 'text/plain');
        // Cache sitemap for 1 hour (content may change)
        $response->header('Cache-Control', 'public, max-age=3600');

        return $response;
    }
}
