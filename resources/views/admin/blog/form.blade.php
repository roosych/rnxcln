@extends('layouts.admin')

@section('title', $post->exists ? 'Edit post' : 'Add post')
@section('page_title', $post->exists ? 'Edit post' : 'Add post')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.blog-posts.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to posts</a>
    </div>

    <form method="POST"
          action="{{ $post->exists ? route('admin.blog-posts.update', $post) : route('admin.blog-posts.store') }}"
          enctype="multipart/form-data" id="blog-post-form">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="row">
            <div class="col-lg-8">

                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-details" type="button" role="tab">Details</button>
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
                                    <label class="admin-form-label" for="title">Title</label>
                                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="slug">Slug</label>
                                    <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $post->slug) }}">
                                    <div class="admin-form-hint">
                                        Leave blank to auto-generate from the title.
                                        @if ($post->exists)
                                            Page: <code>{{ url('/blog/'.$post->slug) }}</code>
                                        @endif
                                    </div>
                                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="excerpt">Excerpt</label>
                                    <textarea id="excerpt" name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
                                    <div class="admin-form-hint">Short teaser shown on blog cards. Also used as the SEO description when no SEO description is set below.</div>
                                    @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="body">Article</label>
                                    <input id="body" type="hidden" name="body" value="{{ old('body', $post->body) }}">
                                    <trix-editor input="body" class="@error('body') is-invalid @enderror"></trix-editor>
                                    @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="author_name">Author</label>
                                    <input type="text" id="author_name" name="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name', $post->author_name ?: setting('site.name')) }}">
                                    @error('author_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-2">
                                    <label class="admin-form-label">Categories</label>
                                    <div class="admin-form-hint mb-2">Blog categories are the services above — pick 1 or 2 to tag this post with. Shown as pill labels on the post's card.</div>
                                    @php $selected = old('categories', $post->categories->pluck('id')->all()); @endphp
                                    <div class="row">
                                        @foreach ($services as $service)
                                            <div class="col-md-6 form-check mb-2">
                                                <input type="checkbox" class="form-check-input" id="category-{{ $service->id }}" name="categories[]" value="{{ $service->id }}" {{ in_array($service->id, $selected) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="category-{{ $service->id }}">{!! $service->title !!}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('categories')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <label class="admin-form-label" for="meta_title">SEO title</label>
                                    <input type="text" id="meta_title" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $post->meta_title) }}">
                                    <div class="admin-form-hint">Shown in the browser tab and search results for this post. Falls back to the title above if left blank.</div>
                                    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-2">
                                    <label class="admin-form-label" for="meta_description">SEO description</label>
                                    <textarea id="meta_description" name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2">{{ old('meta_description', $post->meta_description) }}</textarea>
                                    <div class="admin-form-hint">Falls back to the excerpt above if left blank.</div>
                                    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Images --}}
                    <div class="tab-pane fade" id="tab-images" role="tabpanel">
                        <div class="admin-card">
                            <div class="admin-card-header"><h2>Cover image</h2></div>
                            <div class="admin-card-body">
                                @if ($post->cover_image)
                                    <div style="margin-bottom:10px;"><img src="{{ $post->coverImageUrl() }}" alt="" style="max-width:100%;border-radius:8px;"></div>
                                @endif
                                <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                                <div class="admin-form-hint">Shown on blog cards and at the top of the post.</div>
                                @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                <div class="mb-0 mt-3">
                                    <label class="admin-form-label" for="alt">Image alt text</label>
                                    <input type="text" id="alt" name="alt" class="form-control @error('alt') is-invalid @enderror" value="{{ old('alt', $post->alt) }}">
                                    @error('alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="admin-card">
                            <div class="admin-card-header"><h2>Social sharing image</h2></div>
                            <div class="admin-card-body">
                                @if ($post->og_image)
                                    <div style="margin-bottom:10px;"><img src="{{ $post->ogImageUrl() }}" alt="" style="max-width:100%;border-radius:8px;"></div>
                                @endif
                                <input type="file" name="og_image" class="form-control @error('og_image') is-invalid @enderror" accept="image/*">
                                <div class="admin-form-hint">Shown when this post's link is shared on Facebook, WhatsApp, etc. If left blank, the cover image above is used instead.</div>
                                @error('og_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published', $post->is_published ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Published (visible on site)</label>
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label" for="published_at">Published date</label>
                            <input type="datetime-local" id="published_at" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
                                   value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}">
                            <div class="admin-form-hint">Left blank, a post is stamped with the moment it's first published. Sets the display date and the sort order on the blog.</div>
                            @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Featured ("Popular publications")</label>
                        </div>
                        <button type="submit" class="admin-btn admin-btn-primary w-100 justify-content-center"><i class="fas fa-save"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
