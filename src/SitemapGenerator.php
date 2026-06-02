<?php

namespace Tablo\Seo;

use Kirby\Http\Response;

/**
 * Generates sitemap.txt content
 */
class SitemapGenerator
{
    /**
     * Generates sitemap.txt response.
     *
     * Includes every listed page, plus any unlisted pages explicitly opted in
     * through the `tablo.seo.sitemapIncludeUnlisted` option (matched by id /
     * path, with `*` and `?` wildcards). Pages are skipped when their template
     * matches `tablo.seo.sitemapExcludeTemplates`, or when any ancestor is
     * neither listed nor opted in — so an unlisted branch never leaks its
     * children unless the branch itself is opted in.
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

        [$exactExclude, $wildcardExclude] = self::splitPatterns(
            option('tablo.seo.sitemapExcludeTemplates', [])
        );
        [$exactInclude, $wildcardInclude] = self::splitPatterns(
            option('tablo.seo.sitemapIncludeUnlisted', [])
        );

        foreach (site()->index() as $page) {
            // The page itself must be visible: listed, or an unlisted page that
            // was explicitly opted in by path.
            if (!self::isVisible($page, $exactInclude, $wildcardInclude)) {
                continue;
            }

            // Every ancestor must be visible too.
            foreach ($page->parents() as $parent) {
                if (!self::isVisible($parent, $exactInclude, $wildcardInclude)) {
                    continue 2;
                }
            }

            // Drop pages whose template is excluded.
            $templateName = $page->intendedTemplate()->name();
            if (isset($exactExclude[$templateName])) {
                continue;
            }
            foreach ($wildcardExclude as $pattern) {
                if (fnmatch($pattern, $templateName)) {
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

    /**
     * Determines whether a page may appear in the sitemap.
     *
     * Listed pages always qualify. Unlisted pages qualify only when their id
     * (path) matches one of the `sitemapIncludeUnlisted` patterns.
     *
     * @param \Kirby\Cms\Page $page The page to test
     * @param array<string,bool> $exactInclude Exact page ids keyed for lookup
     * @param array<int,string> $wildcardInclude fnmatch patterns
     * @return bool
     */
    private static function isVisible($page, array $exactInclude, array $wildcardInclude): bool
    {
        if ($page->isListed()) {
            return true;
        }

        $id = $page->id();
        if (isset($exactInclude[$id])) {
            return true;
        }
        foreach ($wildcardInclude as $pattern) {
            if (fnmatch($pattern, $id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Splits a list of patterns into exact matches and fnmatch wildcards.
     *
     * @param array<int,string> $patterns The configured patterns
     * @return array{0:array<string,bool>,1:array<int,string>} Exact lookup map and wildcard list
     */
    private static function splitPatterns(array $patterns): array
    {
        $exact = [];
        $wildcard = [];
        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*') || str_contains($pattern, '?')) {
                $wildcard[] = $pattern;
            } else {
                $exact[$pattern] = true;
            }
        }

        return [$exact, $wildcard];
    }
}
