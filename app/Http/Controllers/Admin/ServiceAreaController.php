<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceAreaRequest;
use App\Models\ServiceArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceAreaController extends Controller
{
    public function index(Request $request): View
    {
        $areas = ServiceArea::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($q) => $q->where('zip', 'like', $term)->orWhere('area', 'like', $term));
            })
            ->orderBy('area')
            ->paginate(25)
            ->withQueryString();

        return view('admin.service-areas.index', compact('areas'));
    }

    public function store(ServiceAreaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        ServiceArea::create($data);

        return redirect()->route('admin.service-areas.index')->with('status', 'Service area created.');
    }

    public function edit(ServiceArea $serviceArea): View
    {
        return view('admin.service-areas.form', ['area' => $serviceArea]);
    }

    public function update(ServiceAreaRequest $request, ServiceArea $serviceArea): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $serviceArea->update($data);

        return redirect()->route('admin.service-areas.index')->with('status', 'Service area updated.');
    }

    public function destroy(ServiceArea $serviceArea): RedirectResponse
    {
        $serviceArea->delete();

        return back()->with('status', 'Service area deleted.');
    }
}
