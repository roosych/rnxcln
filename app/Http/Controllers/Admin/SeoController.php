<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageSeoRequest;
use App\Http\Requests\Admin\SeoAnalyticsRequest;
use App\Models\PageSeo;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function index(): View
    {
        $pages = PageSeo::query()->orderBy('route_name')->get();
        $robots = File::exists(public_path('robots.txt')) ? File::get(public_path('robots.txt')) : '';

        return view('admin.seo.index', [
            'pages' => $pages,
            'robots' => $robots,
            'ga4Id' => setting('seo.ga4_id'),
            'yandexId' => setting('seo.yandex_metrika_id'),
            'customHeadCode' => setting('seo.custom_head_code'),
        ]);
    }

    public function edit(PageSeo $pageSeo): View
    {
        return view('admin.seo.form', ['page' => $pageSeo]);
    }

    public function update(PageSeoRequest $request, PageSeo $pageSeo): RedirectResponse
    {
        $data = $request->validated();

        $data['schema_json'] = empty($data['schema_json']) ? null : json_decode($data['schema_json'], true);

        if ($request->boolean('remove_og_image')) {
            if ($pageSeo->og_image) {
                Storage::disk('public')->delete($pageSeo->og_image);
            }
            $data['og_image'] = null;
        } elseif ($request->hasFile('og_image')) {
            if ($pageSeo->og_image) {
                Storage::disk('public')->delete($pageSeo->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('seo', 'public');
        } else {
            unset($data['og_image']);
        }
        unset($data['remove_og_image']);

        $pageSeo->update($data);

        return redirect()->route('admin.seo.index')->with('status', "SEO for \"{$pageSeo->route_name}\" saved.");
    }

    public function updateAnalytics(SeoAnalyticsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Setting::put('seo', 'ga4_id', $data['ga4_id'] ?? null);
        Setting::put('seo', 'yandex_metrika_id', $data['yandex_metrika_id'] ?? null);
        Setting::put('seo', 'custom_head_code', $data['custom_head_code'] ?? null);

        // robots.txt is served by nginx as a static file, so the setting is
        // written straight through to public/robots.txt on save.
        File::put(public_path('robots.txt'), rtrim((string) ($data['robots_txt'] ?? ''))."\n");

        return back()->with('status', 'SEO settings saved.');
    }
}
