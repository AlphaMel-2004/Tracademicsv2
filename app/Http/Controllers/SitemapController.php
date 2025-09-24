<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        // Generate XML sitemap
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Main pages
        $pages = [
            ['url' => url('/'), 'priority' => '1.0'],
            ['url' => route('login'), 'priority' => '0.8'],
            ['url' => route('register'), 'priority' => '0.8'],
            ['url' => url('/system-status'), 'priority' => '0.7'],
        ];

        foreach ($pages as $page) {
            $sitemap .= '  <url>' . PHP_EOL;
            $sitemap .= '    <loc>' . $page['url'] . '</loc>' . PHP_EOL;
            $sitemap .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $sitemap .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $sitemap .= '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
            $sitemap .= '  </url>' . PHP_EOL;
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}