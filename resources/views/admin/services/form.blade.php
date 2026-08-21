@extends('layouts.admin')

@section('title', $service->exists ? 'Edit service' : 'Add service')
@section('page_title', $service->exists ? 'Edit service' : 'Add service')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to services</a>
    </div>

    <form method="POST"
          action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}"
          enctype="multipart/form-data" id="service-form">
        @csrf
        @if ($service->exists) @method('PUT') @endif

        <div class="row">
            <div class="col-lg-8">

                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-details" type="button" role="tab">Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-checklist" type="button" role="tab">What we clean</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-steps" type="button" role="tab">How we clean it</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-images" type="button" role="tab">Images</button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- Details --}}
                    <div class="tab-pane fade show active" id="tab-details" role="tabpanel">
                        <div class="admin-card">
                            <div class="admin-card-body">

                                <div class="mb-4">
                                    <label class="admin-form-label" for="section">Folder</label>
                                    <select id="section" name="section" class="form-control @error('section') is-invalid @enderror">
                                        <option value="" {{ old('section', $service->section) ? '' : 'selected' }}>No folder — not listed on the Services page, reachable only from its own page</option>
                                        @foreach ($folders as $key => $label)
                                            <option value="{{ $key }}" {{ old('section', $service->section) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="admin-form-hint">This service always has its own page at <code>/services/{slug}</code> — the folder only controls whether (and how) it also appears as a card on the Services page. It has no effect on Home: that's controlled entirely by the "Featured" checkbox below, regardless of folder.</div>
                                    @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="title">Title</label>
                                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $service->title) }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="slug">Slug</label>
                                    <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $service->slug) }}">
                                    <div class="admin-form-hint">
                                        Leave blank to auto-generate from the title.
                                        @if ($service->exists)
                                            Page: <code>{{ url('/services/'.$service->slug) }}</code>
                                        @endif
                                    </div>
                                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="tagline">Tagline</label>
                                    <input type="text" id="tagline" name="tagline" class="form-control @error('tagline') is-invalid @enderror" value="{{ old('tagline', $service->tagline) }}">
                                    @error('tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="text">Description</label>
                                    <textarea id="text" name="text" class="form-control @error('text') is-invalid @enderror" rows="4">{{ old('text', $service->text) }}</textarea>
                                    @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-2">
                                    <label class="admin-form-label" for="link_type">Card links to</label>
                                    <select id="link_type" name="link_type" class="form-control @error('link_type') is-invalid @enderror">
                                        <option value="page" {{ old('link_type', $service->link_type ?? 'page') === 'page' ? 'selected' : '' }}>This service's own page (default)</option>
                                        <option value="contact" {{ old('link_type', $service->link_type) === 'contact' ? 'selected' : '' }}>Contact / booking form</option>
                                        <option value="custom" {{ old('link_type', $service->link_type) === 'custom' ? 'selected' : '' }}>Custom URL</option>
                                    </select>
                                    <div class="admin-form-hint">Only affects the card shown on Home/Services (icon and wide-card sections) — the "Details" link on a long card and this service's own page always work regardless.</div>
                                    @error('link_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-2" id="link_url_group">
                                    <label class="admin-form-label" for="link_url">Custom URL</label>
                                    <input type="text" id="link_url" name="link_url" class="form-control @error('link_url') is-invalid @enderror" value="{{ old('link_url', $service->link_url) }}" placeholder="https://…">
                                    @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="meta_title">SEO title</label>
                                    <input type="text" id="meta_title" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $service->meta_title) }}">
                                    <div class="admin-form-hint">Shown in the browser tab and search results for this service's page. Falls back to the title above if left blank.</div>
                                    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-2">
                                    <label class="admin-form-label" for="meta_description">SEO description</label>
                                    <textarea id="meta_description" name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2">{{ old('meta_description', $service->meta_description) }}</textarea>
                                    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- What we clean --}}
                    <div class="tab-pane fade" id="tab-checklist" role="tabpanel">
                        <div class="admin-card">
                            <div class="admin-card-body">
                                <div class="admin-form-hint mb-3">Shown as "What we clean" on this service's own page, the full checklist on a long card, and as an item count (e.g. "5 items") on a wide card. Leave empty to skip that block.</div>
                                <label class="admin-form-label">Check-list items</label>
                                <ul id="items-list" class="list-group mb-3"></ul>
                                <button type="button" id="add-item-btn" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-plus"></i> Add item</button>
                                <div class="admin-form-hint mt-2">Drag the handle to reorder.</div>
                                <textarea id="items" name="items" style="display:none;">{{ old('items', implode("\n", $service->items ?? [])) }}</textarea>
                                @error('items')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- How we clean it --}}
                    <div class="tab-pane fade" id="tab-steps" role="tabpanel">
                        <div class="admin-card">
                            <div class="admin-card-body">
                                <div class="admin-form-hint mb-3">Shown as "How we clean it" on this service's own page — a numbered list of steps. Leave empty to skip that block.</div>
                                <ul id="steps-list" class="list-group mb-3">
                                    @foreach ($service->exists ? $service->steps : [] as $step)
                                        <li class="list-group-item" data-step>
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="fas fa-grip-vertical drag-handle" style="cursor:grab;margin-top:8px;"></i>
                                                <div class="flex-grow-1">
                                                    <input type="hidden" name="steps[][id]" value="{{ $step->id }}">
                                                    <input type="text" name="steps[][title]" class="form-control form-control-sm mb-2 step-title" value="{{ $step->title }}" placeholder="Step title">
                                                    <textarea name="steps[][text]" class="form-control form-control-sm step-text" rows="2" placeholder="Step description">{{ $step->text }}</textarea>
                                                </div>
                                                <button type="button" class="admin-btn admin-btn-danger admin-btn-sm remove-step-btn"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <button type="button" id="add-step-btn" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-plus"></i> Add step</button>
                                <div class="admin-form-hint mt-2">Drag the handle to reorder. A <code>&lt;br&gt;</code> in the title controls the line wrap.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Images --}}
                    <div class="tab-pane fade" id="tab-images" role="tabpanel">
                        <div class="admin-card">
                            <div class="admin-card-header"><h2>Image</h2></div>
                            <div class="admin-card-body">
                                @if ($service->image)
                                    <div style="margin-bottom:10px;"><img src="{{ $service->imageUrl() }}" alt="" style="max-width:100%;border-radius:8px;"></div>
                                @endif
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                <div class="admin-form-hint">Used as the photo on a long card, or the icon on an icon-grid card.</div>
                                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                <div class="mb-0 mt-3">
                                    <label class="admin-form-label" for="alt">Image alt text</label>
                                    <input type="text" id="alt" name="alt" class="form-control @error('alt') is-invalid @enderror" value="{{ old('alt', $service->alt) }}">
                                    @error('alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="admin-card">
                            <div class="admin-card-header"><h2>Before / after</h2></div>
                            <div class="admin-card-body">
                                <div class="admin-form-hint mb-3">Shown as a drag-to-compare slider on this service's own page. Leave both blank to skip that block.</div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="admin-form-label">Before</label>
                                        @if ($service->before_image)
                                            <div style="margin-bottom:10px;"><img src="{{ $service->beforeImageUrl() }}" alt="" style="max-width:100%;border-radius:8px;"></div>
                                        @endif
                                        <input type="file" name="before_image" class="form-control @error('before_image') is-invalid @enderror" accept="image/*">
                                        @error('before_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="admin-form-label">After</label>
                                        @if ($service->after_image)
                                            <div style="margin-bottom:10px;"><img src="{{ $service->afterImageUrl() }}" alt="" style="max-width:100%;border-radius:8px;"></div>
                                        @endif
                                        <input type="file" name="after_image" class="form-control @error('after_image') is-invalid @enderror" accept="image/*">
                                        @error('after_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-card">
                    <div class="admin-card-header"><h2>Options</h2></div>
                    <div class="admin-card-body">
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active (visible on site)</label>
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $service->is_featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Featured (top pick on Home)</label>
                        </div>
                        <div class="admin-form-hint mb-4">Works from any folder. Featured services show at the top of the homepage (photo card if this service has an image, compact card otherwise); everything else not featured shows further down, in random order.</div>
                        <div class="admin-form-hint mb-4">Order among featured services is set by dragging rows on the <a href="{{ route('admin.services.index') }}">services list</a>.</div>
                        <button type="submit" class="admin-btn admin-btn-primary w-100 justify-content-center"><i class="fas fa-save"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // admin.js is loaded as a module (deferred), so it hasn't necessarily set
    // window.Sortable / window.bootstrap yet when a classic inline script at
    // this point in the body would normally run — waiting for
    // DOMContentLoaded guarantees deferred scripts have already executed.
    var form = document.getElementById('service-form');
    var linkTypeSelect = document.getElementById('link_type');
    var linkUrlGroup = document.getElementById('link_url_group');

    function applyLinkUrlVisibility() {
        linkUrlGroup.style.display = linkTypeSelect.value === 'custom' ? '' : 'none';
    }

    linkTypeSelect.addEventListener('change', applyLinkUrlVisibility);
    applyLinkUrlVisibility();

    // Reorderable checklist: rows live only in the DOM, synced into the
    // hidden #items textarea (newline-joined, same format the controller
    // already parses) right before submit.
    var itemsList = document.getElementById('items-list');
    var itemsTextarea = document.getElementById('items');
    var addItemBtn = document.getElementById('add-item-btn');

    function addItemRow(value) {
        var li = document.createElement('li');
        li.className = 'list-group-item d-flex align-items-center gap-2';
        li.innerHTML =
            '<i class="fas fa-grip-vertical drag-handle" style="cursor:grab;"></i>' +
            '<input type="text" class="form-control form-control-sm item-input">' +
            '<button type="button" class="admin-btn admin-btn-danger admin-btn-sm remove-item-btn"><i class="fas fa-trash"></i></button>';
        li.querySelector('.item-input').value = value || '';
        li.querySelector('.remove-item-btn').addEventListener('click', function () {
            li.remove();
        });
        itemsList.appendChild(li);
    }

    itemsTextarea.value.split('\n').map(function (v) { return v.trim(); }).filter(Boolean).forEach(addItemRow);

    addItemBtn.addEventListener('click', function () {
        addItemRow('');
    });

    if (window.Sortable) {
        window.Sortable.create(itemsList, { handle: '.drag-handle', animation: 150 });
    }

    // Reorderable steps: each row already carries real steps[][id/title/text]
    // inputs, so a drag-reorder is reflected in submission order directly —
    // nothing to sync before submit.
    var stepsList = document.getElementById('steps-list');
    var addStepBtn = document.getElementById('add-step-btn');

    function addStepRow() {
        var li = document.createElement('li');
        li.className = 'list-group-item';
        li.setAttribute('data-step', '');
        li.innerHTML =
            '<div class="d-flex align-items-start gap-2">' +
                '<i class="fas fa-grip-vertical drag-handle" style="cursor:grab;margin-top:8px;"></i>' +
                '<div class="flex-grow-1">' +
                    '<input type="hidden" name="steps[][id]" value="">' +
                    '<input type="text" name="steps[][title]" class="form-control form-control-sm mb-2 step-title" placeholder="Step title">' +
                    '<textarea name="steps[][text]" class="form-control form-control-sm step-text" rows="2" placeholder="Step description"></textarea>' +
                '</div>' +
                '<button type="button" class="admin-btn admin-btn-danger admin-btn-sm remove-step-btn"><i class="fas fa-trash"></i></button>' +
            '</div>';
        stepsList.appendChild(li);
        li.querySelector('.remove-step-btn').addEventListener('click', function () {
            li.remove();
        });
    }

    stepsList.querySelectorAll('.remove-step-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            btn.closest('li').remove();
        });
    });

    addStepBtn.addEventListener('click', addStepRow);

    if (window.Sortable) {
        window.Sortable.create(stepsList, { handle: '.drag-handle', animation: 150 });
    }
});
</script>
@endpush
