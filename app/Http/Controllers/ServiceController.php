<?php

namespace App\Http\Controllers;

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
        ]);
    }

    /**
     * Up to 4 other active services — same folder first, then the rest, by
     * admin order. Only services with items qualify, since the card shown
     * for these (x-service-wide) displays an item count.
     */
    private function otherServices(Service $service): Collection
    {
        return Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            // items with nothing in them are stored as the JSON string '[]',
            // not SQL NULL, so whereNotNull() alone wouldn't catch them.
            ->whereNotNull('items')
            ->whereRaw("items::text != '[]'")
            ->orderByRaw('section = ? desc', [$service->section])
            ->orderBy('sort_order')
            ->limit(4)
            ->get();
    }
}
