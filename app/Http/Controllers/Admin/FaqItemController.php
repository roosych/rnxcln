<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqItemRequest;
use App\Models\FaqItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqItemController extends Controller
{
    public function index(): View
    {
        $items = FaqItem::query()->orderBy('sort_order')->get();

        return view('admin.faq.index', compact('items'));
    }

    public function store(FaqItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] = FaqItem::max('sort_order') + 1;

        FaqItem::create($data);

        return redirect()->route('admin.faq.index')->with('status', 'FAQ item created.');
    }

    public function edit(FaqItem $faqItem): View
    {
        return view('admin.faq.form', compact('faqItem'));
    }

    public function update(FaqItemRequest $request, FaqItem $faqItem): RedirectResponse
    {
        // sort_order isn't part of this form — only the drag-to-reorder list
        // on the index page changes it — so it's left out of the update data.
        $faqItem->update($request->validated());

        return redirect()->route('admin.faq.index')->with('status', 'FAQ item updated.');
    }

    public function destroy(FaqItem $faqItem): RedirectResponse
    {
        $faqItem->delete();

        return back()->with('status', 'FAQ item deleted.');
    }

    public function reorder(Request $request): void
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach (array_values($ids) as $order => $id) {
            FaqItem::whereKey($id)->update(['sort_order' => $order]);
        }
    }
}
