<?php

namespace Tablo\Seo;

use Kirby\Http\Response;

/**
 * Generates favicon.ico
 */
class FaviconGenerator
{
    /**
     * Returns a simple blue favicon.ico.
     * Hardcoded minimal PNG format for maximum efficiency.
     *
     * @return \Kirby\Http\Response The favicon response
     */
    public static function generate(): Response
    {
        // Minimal 16x16 blue PNG (#00f) - compressed, ~100 bytes
        $png = hex2bin('89504E470D0A1A0A0000000D4948445200000010000000100802000000909168360000001949444154789C636460F8CF400A602249F5A886510D434A03003E30011F5A25BDCA0000000049454E44AE42');

        $response = new Response($png, 'image/x-icon');
        // Cache favicon for 1 year (static asset)
        $response->header('Cache-Control', 'public, max-age=31536000, immutable');

        return $response;
    }
}
