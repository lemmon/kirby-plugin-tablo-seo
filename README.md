# Tablo SEO

Small SEO toolkit for [Tablo Themes](https://tablo.supply/). Provides custom blueprints, favicon generation, Open Graph image generation, robots.txt, sitemap generation, and helpers for basic SEO needs in Kirby CMS.

This plugin is intended for Tablo templates and is not meant to replace more advanced Kirby SEO plugins. For comprehensive SEO functionality, consider plugins from the [Kirby SEO plugins collection](https://plugins.getkirby.com/topics/seo).

## Social Card Images

Pages using the `tabs/seo` blueprint can select an optional custom Open Graph and Twitter card image through `seo_og_image`. A 1200 x 630 px raster image is recommended.

- `$page->ogImage()` returns the custom file URL when selected, otherwise it returns a generated Tablo card URL.
- `$page->ogImageFile()` returns the selected custom file or `null`, for templates that need its dimensions or alt metadata.

## Install Notes

```sh
# Add the plugin as a submodule
git submodule add https://github.com/lemmon/kirby-plugin-tablo-seo site/plugins/tablo-seo
```

## Sitemap Options

The sitemap lists every listed page by default. Two options refine that set
(both accept `*` and `?` wildcards):

| Option                    | Matches against | Effect                                    |
| ------------------------- | --------------- | ----------------------------------------- |
| `sitemapExcludeTemplates` | template name   | Removes matching pages from the sitemap.  |
| `sitemapIncludeUnlisted`  | page id (path)  | Opts matching **unlisted** pages back in. |

Pages whose template is excluded never appear. A page also needs every
ancestor to be visible (listed, or opted in via `sitemapIncludeUnlisted`), so
an unlisted branch never leaks its children unless the branch itself is opted
in. Listed children of an opted-in unlisted parent are included automatically;
unlisted children stay out.

```php
// site/config/config.php
return [
    'tablo.seo' => [
        // Unlisted "products" index should be crawlable, and so should its
        // listed product children — but not any unlisted ones.
        'sitemapIncludeUnlisted' => ['products'],
    ],
];
```
