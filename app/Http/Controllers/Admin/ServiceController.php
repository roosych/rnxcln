<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceFolderNamesRequest;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public const LINK_TYPES = ['page', 'contact', 'custom'];

    public const FOLDER_HINTS = [
        'core' => 'Large card with photo — always shown on Services.',
        'home-office' => 'Wide card with item count, no photo — always shown on Services.',
    ];

    private const IMAGE_FIELDS = ['image', 'before_image', 'after_image', 'og_image'];

    public function index(): View
    {
        return view('admin.services.index', [
            'folders' => Service::folderNames(),
            'folderHints' => self::FOLDER_HINTS,
            'services' => Service::orderBy('sort_order')->get(),
        ]);
    }

    public function updateFolderNames(ServiceFolderNamesRequest $request): RedirectResponse
    {
        Setting::put('site', 'service_folders', $request->validated());

        return redirect()->route('admin.services.index')->with('status', 'Folder names saved.');
    }

    public function create(Request $request): View
    {
        $service = new Service(['section' => $request->string('folder')->value() ?: null]);

        return view('admin.services.form', ['service' => $service, 'folders' => Service::folderNames()]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $this->withDefaults($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['sort_order'] = Service::max('sort_order') + 1;

        foreach (self::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('services', 'public');
            }
        }

        $service = Service::create($data);
        $this->syncSteps($service, $request->input('steps', []));

        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', ['service' => $service, 'folders' => Service::folderNames()]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        // sort_order isn't part of this form — only the drag-to-reorder list
        // on the index page changes it — so it's left out of the update data.
        $data = $this->withDefaults($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        unset($data['sort_order']);

        foreach (self::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                if ($service->$field) {
                    Storage::disk('public')->delete($service->$field);
                }
                $data[$field] = $request->file($field)->store('services', 'public');
            }
        }

        $service->update($data);
        $this->syncSteps($service, $request->input('steps', []));

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        foreach (self::IMAGE_FIELDS as $field) {
            if ($service->$field) {
                Storage::disk('public')->delete($service->$field);
            }
        }

        // The FK is nullOnDelete, so a deleted service's steps aren't
        // cascade-removed automatically — without this they'd become
        // permanently orphaned (service_id and group both null).
        $service->steps()->delete();
        $service->delete();

        return back()->with('status', 'Service deleted.');
    }

    public function reorder(Request $request): void
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach (array_values($ids) as $order => $id) {
            Service::whereKey($id)->update(['sort_order' => $order]);
        }
    }

    private function withDefaults(ServiceRequest $request): array
    {
        $data = $request->validated();

        $data['items'] = collect(explode("\n", (string) $request->input('items')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['link_url'] = $data['link_type'] === 'custom' ? $data['link_url'] : null;

        foreach (self::IMAGE_FIELDS as $field) {
            unset($data[$field]);
        }
        unset($data['steps']);

        return $data;
    }

    /**
     * "How we clean it" steps, edited inline on the service form. Rows come
     * in as steps[][id]/steps[][title]/steps[][text] — PHP auto-indexes
     * repeated `name="x[][y]"` fields by submission order, so a drag-reorder
     * in the browser is already reflected in array order here, no separate
     * hidden-field sync needed.
     */
    private function syncSteps(Service $service, array $rows): void
    {
        $keepIds = [];

        foreach (array_values($rows) as $i => $row) {
            $title = trim($row['title'] ?? '');
            if ($title === '') {
                continue;
            }

            $step = ! empty($row['id'])
                ? ProcessStep::where('service_id', $service->id)->whereKey($row['id'])->first()
                : null;

            $attributes = [
                'service_id' => $service->id,
                'title' => $title,
                'text' => trim($row['text'] ?? ''),
                'sort_order' => $i,
            ];

            $step = $step ? tap($step)->update($attributes) : ProcessStep::create($attributes);
            $keepIds[] = $step->id;
        }

        ProcessStep::where('service_id', $service->id)->whereNotIn('id', $keepIds)->delete();
    }
}
