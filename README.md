# Tablo SEO

Small SEO toolkit for [Tablo Themes](https://tablo.supply/). Provides custom blueprints, favicon generation, Open Graph image generation, robots.txt, sitemap generation, and helpers for basic SEO needs in Kirby CMS.

This plugin is intended for Tablo templates and is not meant to replace more advanced Kirby SEO plugins. For comprehensive SEO functionality, consider plugins from the [Kirby SEO plugins collection](https://plugins.getkirby.com/topics/seo).

## Social Card Images

Pages using the `tabs/seo` blueprint can select an optional custom Open Graph and Twitter card image through `seo_og_image`. A 1200 x 630 px raster image is recommended.

- `$page->ogImage()` returns the custom file URL when selected, otherwise it returns a generated Tablo card URL.
- `$page->ogImageFile()` returns the selected custom file or `null`, for templates that need its dimensions or alt metadata.

## Robots Meta Tag

Pages using the `tabs/seo` blueprint also expose `robots_noindex` and
`robots_nofollow` toggles. `$page->metaRobots()` combines those with
`tablo.seo.forceNoindex` into a ready-to-use content value (e.g.
`"noindex, noarchive"`), or `null` when no directive applies:

```twig
{% set robots = page.metaRobots %}
{% if robots %}
<meta name="robots" content="{{ robots }}">
{% endif %}
```

A no-index page (globally or individually) is also automatically excluded
from the sitemap — see [Sitemap Options](#sitemap-options).

## Install Notes

```sh
# Add the plugin as a submodule
git submodule add https://github.com/lemmon/kirby-plugin-tablo-seo site/plugins/tablo-seo
```

## Sitemap Options

The sitemap lists every listed page by default. Inclusion is decided in two
steps:

1. **Visibility** — is the page a candidate at all? Listed pages always are;
   unlisted pages are candidates only when opted in via
   `sitemapIncludeUnlisted`. Every ancestor must also be visible, so an
   unlisted branch never leaks its children unless the branch itself is
   opted in. Listed children of an opted-in unlisted parent are included
   automatically; unlisted children stay out.
2. **Exclusion** — a visible page is still dropped if its template is
   excluded, or if it's no-index.

| Option                    | Matches against | Effect                                                 |
| ------------------------- | --------------- | ------------------------------------------------------ |
| `sitemapIncludeUnlisted`  | page id (path)  | Opts matching **unlisted** pages back in (visibility). |
| `sitemapExcludeTemplates` | template name   | Removes matching pages from the sitemap (exclusion).   |
| `forceNoindex`            | —               | Global kill switch: excludes every page (exclusion).   |

`sitemapIncludeUnlisted` and `sitemapExcludeTemplates` both accept `*` and
`?` wildcards. A page is also excluded when its `robots_noindex` field (SEO
tab) is enabled — per-page, independent of `forceNoindex`.

```php
// site/config/config.php
return [
    'tablo.seo' => [
        // Unlisted "products" index should be crawlable, and so should its
        // listed product children — but not any unlisted ones.
        'sitemapIncludeUnlisted' => ['products'],

        // Keep staging out of search engines entirely.
        'forceNoindex' => getenv('APP_ENV') === 'staging',
    ],
];
```
