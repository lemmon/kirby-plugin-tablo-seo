<?php

namespace Tablo\Seo;

use Kirby\Http\Response;

/**
 * Generates robots.txt content
 */
class RobotsGenerator
{
    /**
     * Generates robots.txt response.
     *
     * @return \Kirby\Http\Response The robots.txt response
     */
    public static function generate(): Response
    {
        $disallow = option('tablo.seo.robotsDisallow', '');

        $data = ['User-agent: *'];

        // Handle disallow - can be string or array
        if (is_array($disallow)) {
            foreach ($disallow as $path) {
                $data[] = 'Disallow: ' . $path;
            }
        } else {
            $data[] = 'Disallow: ' . $disallow;
        }

        // Add sitemaps if available and active
        if (option('tablo.seo.sitemapActive', true)) {
            $sitemaps = site()->sitemaps();
            if (is_array($sitemaps) && !empty($sitemaps)) {
                foreach ($sitemaps as $sitemap) {
                    $data[] = 'Sitemap: ' . $sitemap;
                }
            }
        }

        $response = new Response(join("\n", $data), 'text/plain');
        // Cache robots.txt for 1 hour (may change with config)
        $response->header('Cache-Control', 'public, max-age=3600');

        return $response;
    }
}
