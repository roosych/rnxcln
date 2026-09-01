<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\FaqItem;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404);

        return view('pages.service-detail', [
            'service' => $service,
            'faqItems' => FaqItem::orderBy('sort_order')->get(),
            'otherServices' => $this->otherServices($service),
            'articles' => BlogPost::published()
                ->whereHas('categories', fn ($q) => $q->whereKey($service->id))
                ->with('categories')
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }

    /**
     * Up to 4 other active services — same folder first, then the rest, by
     * admin order. Only services with items qualify, since the card shown
     * for these (x-service-wide) displays an item count.
     */
    private function otherServices(Service $service): Collection
    {
        // Filtering out empty `items` happens in PHP rather than SQL: an
        // empty items list is stored as the JSON string '[]', not SQL NULL,
        // and testing for that needs driver-specific raw SQL (this bit us
        // once already when dev ran Postgres and prod ran MySQL).
        return Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->orderByRaw('section = ? desc', [$service->section])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Service $other) => ! empty($other->items))
            ->take(4)
            ->values();
    }
}
