<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\PageSeo;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', 3600, function () {
            $pages = PageSeo::query()->where('robots', 'index')->get();

            $urls = $pages->map(function (PageSeo $page) {
                return [
                    'loc' => Route::has($page->route_name) ? route($page->route_name) : url('/'),
                    'lastmod' => $page->updated_at->toAtomString(),
                ];
            });

            $urls = $urls->merge(Service::where('is_active', true)->get()->map(fn (Service $service) => [
                'loc' => route('services.show', $service),
                'lastmod' => $service->updated_at->toAtomString(),
            ]));

            $urls = $urls->merge(BlogPost::published()->get()->map(fn (BlogPost $post) => [
                'loc' => route('blog.show', $post),
                'lastmod' => $post->updated_at->toAtomString(),
            ]));

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
            foreach ($urls as $url) {
                $xml .= "  <url>\n";
                $xml .= '    <loc>'.e($url['loc'])."</loc>\n";
                $xml .= '    <lastmod>'.$url['lastmod']."</lastmod>\n";
                $xml .= "  </url>\n";
            }
            $xml .= '</urlset>';

            return $xml;
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
