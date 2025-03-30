<?php

/**
 * Tablo SEO Plugin for Kirby CMS
 *
 * (c) Tablo.Supply
 *
 * This plugin provides a custom blueprint for SEO settings in the Tablo theme.
 */

 use Kirby;

Kirby::plugin('tablo/seo', [
    'blueprints' => [
        'tabs/seo' => __DIR__ . '/blueprints/tabs/seo.yml',
    ],
]);
