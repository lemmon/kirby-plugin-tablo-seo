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

        $excludeTemplates = option('tablo.seo.sitemapExcludeTemplates', []);
        $exactExclude = [];
        $wildcardExclude = [];
        foreach ($excludeTemplates as $pattern) {
            if (str_contains($pattern, '*') || str_contains($pattern, '?')) {
                $wildcardExclude[] = $pattern;
            } else {
                $exactExclude[$pattern] = true;
            }
        }

        foreach (site()->index()->listed() as $page) {
            $templateName = $page->intendedTemplate()->name();
            if (isset($exactExclude[$templateName])) {
                continue;
            }
            foreach ($wildcardExclude as $pattern) {
                if (fnmatch($pattern, $templateName)) {
                    continue 2;
                }
            }
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
