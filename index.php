<?php

use Kirby\Filesystem\F;
use Tablo\Seo\FaviconGenerator;
use Tablo\Seo\OgImageGenerator;
use Tablo\Seo\PageExtensions;
use Tablo\Seo\RobotsGenerator;
use Tablo\Seo\SiteExtensions;
use Tablo\Seo\SitemapGenerator;

// Register autoloader for plugin classes
F::loadClasses([
    'Tablo\Seo\FaviconGenerator' => 'src/FaviconGenerator.php',
    'Tablo\Seo\OgImageGenerator' => 'src/OgImageGenerator.php',
    'Tablo\Seo\PageExtensions' => 'src/PageExtensions.php',
    'Tablo\Seo\RobotsGenerator' => 'src/RobotsGenerator.php',
    'Tablo\Seo\SiteExtensions' => 'src/SiteExtensions.php',
    'Tablo\Seo\SitemapGenerator' => 'src/SitemapGenerator.php',
], __DIR__);

/**
 * Tablo SEO Plugin for Kirby CMS
 *
 * Small SEO toolkit providing custom blueprints and helpers for Tablo themes.
 * See README.md for details.
 *
 * @link https://tablo.supply/ Tablo themes marketplace
 * @see README.md
 */

Kirby::plugin('tablo/seo', [
    'options' => [
        'faviconActive' => true,
        'robotsActive' => true,
        'robotsDisallow' => '',
        'sitemapActive' => true,
        'sitemapExcludeTemplates' => [],
        'sitemapIncludeUnlisted' => [],
    ],
    'blueprints' => [
        'tabs/seo' => __DIR__ . '/blueprints/tabs/seo.yml',
    ],
    'pageMethods' => [
        'ogImage' => fn (array $props = []) => PageExtensions::ogImage($this, $props),
    ],
    'siteMethods' => [
        'sitemaps' => fn () => SiteExtensions::sitemaps($this),
    ],
    'routes' => function ($kirby) {
        $routes = [];

        if (option('tablo.seo.faviconActive', true)) {
            $routes[] = [
                'pattern' => 'favicon.ico',
                'action' => fn () => FaviconGenerator::generate(),
            ];
        }

        if (option('tablo.seo.robotsActive', true)) {
            $routes[] = [
                'pattern' => 'robots.txt',
                'action' => fn () => RobotsGenerator::generate(),
            ];
        }

        if (option('tablo.seo.sitemapActive', true)) {
            $routes[] = [
                'pattern' => 'sitemap.txt',
                'action' => fn () => SitemapGenerator::generate(),
            ];
            $routes[] = [
                'pattern' => 'sitemap',
                'action' => fn () => go('sitemap.txt', 301),
            ];
        }

        return $routes;
    },
]);
