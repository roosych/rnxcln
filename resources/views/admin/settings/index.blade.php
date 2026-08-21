@extends('layouts.admin')

@section('title', 'Site settings')
@section('page_title', 'Site settings')

@section('content')

    @include('admin.settings._tabs', ['active' => $group])

    @if ($group === 'company')
        @php $stats = setting('site.stats'); $address = setting('site.address'); @endphp
        <div class="admin-card">
            <div class="admin-card-header"><h2>Company & contacts</h2></div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'company') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <h3 class="admin-form-subheading">Branding</h3>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="admin-form-label">Logo (header, dark background)</label>
                            <div style="margin-bottom:10px;"><img src="{{ logo_url('dark') }}" alt="" style="max-height:60px;"></div>
                            <div class="admin-form-hint mb-2">{{ setting('site.logo_dark') ? 'Currently: your uploaded logo.' : 'Currently: the built-in wordmark — nothing uploaded yet.' }}</div>
                            <input type="file" name="logo_dark" class="form-control @error('logo_dark') is-invalid @enderror" accept="image/*">
                            @error('logo_dark')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="admin-form-label">Logo (footer, light/white version)</label>
                            <div style="margin-bottom:10px;background:#333;display:inline-block;padding:6px;border-radius:6px;"><img src="{{ logo_url('light') }}" alt="" style="max-height:60px;"></div>
                            <div class="admin-form-hint mb-2">{{ setting('site.logo_light') ? 'Currently: your uploaded logo.' : 'Currently: the built-in wordmark — nothing uploaded yet.' }}</div>
                            <input type="file" name="logo_light" class="form-control @error('logo_light') is-invalid @enderror" accept="image/*">
                            @error('logo_light')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="admin-form-label">Favicon</label>
                            @if ($favicon = setting('site.favicon'))
                                <div style="margin-bottom:10px;"><img src="{{ asset('storage/'.$favicon) }}" alt="" style="max-height:32px;"></div>
                                <div class="admin-form-hint mb-2">Currently: your uploaded favicon.</div>
                            @else
                                <div class="admin-form-hint mb-2">Currently: none — the browser tab has no icon until one is uploaded (no built-in fallback).</div>
                            @endif
                            <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" accept="image/*">
                            @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="admin-form-hint mb-4">Leave any of these blank to keep the current file — a new upload replaces it.</div>

                    <hr>
                    <h3 class="admin-form-subheading">Contact details</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4"><label class="admin-form-label">Company name</label><input type="text" name="name" class="form-control" value="{{ old('name', setting('site.name')) }}"></div>
                        <div class="col-md-3 mb-4"><label class="admin-form-label">Phone (display)</label><input type="text" name="phone" class="form-control" value="{{ old('phone', setting('site.phone')) }}"></div>
                        <div class="col-md-3 mb-4"><label class="admin-form-label">Phone (E.164)</label><input type="text" name="phone_e164" class="form-control" value="{{ old('phone_e164', setting('site.phone_e164')) }}"></div>
                    </div>
                    <div class="mb-4"><label class="admin-form-label">Email</label><input type="text" name="email" class="form-control" value="{{ old('email', setting('site.email')) }}"></div>

                    <h3 class="admin-form-subheading">Address</h3>
                    <div class="row">
                        <div class="col-md-4 mb-4"><label class="admin-form-label">City</label><input type="text" name="address_city" class="form-control" value="{{ old('address_city', $address['city']) }}"></div>
                        <div class="col-md-4 mb-4"><label class="admin-form-label">Address line 1</label><input type="text" name="address_line_1" class="form-control" value="{{ old('address_line_1', $address['line_1']) }}"></div>
                        <div class="col-md-4 mb-4"><label class="admin-form-label">Address line 2</label><input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2', $address['line_2']) }}"></div>
                    </div>

                    <h3 class="admin-form-subheading">Stats</h3>
                    <div class="row">
                        <div class="col-md-3 mb-4"><label class="admin-form-label">Jobs/year</label><input type="number" name="stats_jobs" class="form-control" value="{{ old('stats_jobs', $stats['jobs']) }}"></div>
                        <div class="col-md-3 mb-4"><label class="admin-form-label">Years in business</label><input type="number" name="stats_years" class="form-control" value="{{ old('stats_years', $stats['years']) }}"></div>
                        <div class="col-md-3 mb-4"><label class="admin-form-label">Founded (year)</label><input type="number" name="stats_since" class="form-control" value="{{ old('stats_since', $stats['since']) }}"></div>
                        <div class="col-md-3 mb-4"><label class="admin-form-label">Rating</label><input type="text" name="stats_rating" class="form-control" value="{{ old('stats_rating', $stats['rating']) }}"></div>
                    </div>

                    <h3 class="admin-form-subheading">Footer</h3>
                    <div class="mb-4">
                        <label class="admin-form-label">Footer SEO text</label>
                        <input type="text" name="footer_seo_text" class="form-control" value="{{ old('footer_seo_text', setting('site.footer_seo_text', 'Carpet, rug & upholstery cleaning in Chicago, IL')) }}">
                        <div class="admin-form-hint">Shown at the very bottom of every page: "© {{ date('Y') }} <em>this text</em>. All rights reserved." — the year and "All rights reserved" are automatic, this is just the description in between.</div>
                    </div>

                    <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    @endif

    @if ($group === 'hours')
        <div class="admin-card">
            <div class="admin-card-header"><h2>Working hours</h2></div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'hours') }}">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="admin-form-label">One line per entry</label>
                        <textarea name="lines" class="form-control" rows="4">{{ old('lines', implode("\n", setting('site.hours', []))) }}</textarea>
                    </div>
                    <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    @endif

    @if ($group === 'socials')
        @php $socialLines = collect(setting('site.socials', []))->map(fn ($s) => "{$s['icon']}|{$s['url']}")->implode("\n"); @endphp
        <div class="admin-card">
            <div class="admin-card-header"><h2>Social links</h2></div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'socials') }}">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="admin-form-label">One per line: "icon class|url"</label>
                        <textarea name="lines" class="form-control" rows="4" placeholder="fab fa-instagram|https://instagram.com/you">{{ old('lines', $socialLines) }}</textarea>
                    </div>
                    <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    @endif

    @if ($group === 'about')
        <div class="admin-card">
            <div class="admin-card-header"><h2>About page & "a few words about us" block</h2></div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'about') }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="admin-form-label">Section title</label>
                        <input type="text" name="section_title" class="form-control" value="{{ old('section_title', setting('about.section_title', 'A few words about us')) }}">
                    </div>
                    <div class="mb-4">
                        <label class="admin-form-label">Jobs-counter caption</label>
                        <input type="text" name="stats_caption" class="form-control" value="{{ old('stats_caption', setting('about.stats_caption', 'rugs, sofas and armchairs <br>cleaned by our team last year')) }}">
                        <div class="admin-form-hint">Shown next to the "jobs per year" counter. A <code>&lt;br&gt;</code> controls the line wrap.</div>
                    </div>
                    <div class="mb-4">
                        <label class="admin-form-label">Years-counter caption</label>
                        <input type="text" name="years_caption" class="form-control" value="{{ old('years_caption', setting('about.years_caption', 'carpet and sofa <br>cleaning.')) }}">
                        <div class="admin-form-hint">Follows the years-in-business number, e.g. "12 years of [this].</div>
                    </div>
                    <div class="mb-4">
                        <label class="admin-form-label">"Since" prefix</label>
                        <input type="text" name="since_prefix" class="form-control" value="{{ old('since_prefix', setting('about.since_prefix', 'From the Loop to Naperville, since')) }}">
                        <div class="admin-form-hint">The founding year from the Company tab is appended automatically.</div>
                    </div>
                    <div class="mb-4">
                        <label class="admin-form-label">"Safe for..." headline</label>
                        <input type="text" name="safe_headline" class="form-control" value="{{ old('safe_headline', setting('about.safe_headline', 'Safe for wool, <br>velvet, kids <br>and pets.')) }}">
                    </div>
                    <div class="mb-4">
                        <label class="admin-form-label">"Safe for..." body</label>
                        <textarea name="safe_body" class="form-control" rows="3">{{ old('safe_body', setting('about.safe_body', 'Fabric-tested eco-certified solutions, <br>HEPA extraction and no sticky <br>residue left in the pile.')) }}</textarea>
                    </div>

                    <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    @endif

    @if ($group === 'home')
        <div class="admin-card">
            <div class="admin-card-header"><h2>Home page copy</h2></div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'home') }}">
                    @csrf @method('PUT')

                    <h3 class="admin-form-subheading">Hero</h3>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="admin-form-label">Hero line 1</label>
                            <input type="text" name="hero_line_1" class="form-control" value="{{ old('hero_line_1', setting('home.hero_line_1', 'Carpets, Sofas')) }}">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="admin-form-label">Hero line 2</label>
                            <input type="text" name="hero_line_2" class="form-control" value="{{ old('hero_line_2', setting('home.hero_line_2', '&amp; <img src="'.asset('img/ui/t3.jpg').'" alt="image" class="mil-text-image mil-sm-hidden"> Armchairs <img src="'.asset('img/ui/t2.jpg').'" alt="image" class="mil-text-image mil-long mil-sm-hidden">')) }}">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="admin-form-label">Hero line 3</label>
                            <input type="text" name="hero_line_3" class="form-control" value="{{ old('hero_line_3', setting('home.hero_line_3', 'Cleaned <img src="'.asset('img/ui/t1.jpg').'" alt="image" class="mil-text-image mil-sm-hidden"> in Chicago')) }}">
                        </div>
                    </div>
                    <div class="admin-form-hint mb-4">Lines 2 and 3 may contain the decorative inline image markup used between words — leave the HTML alone unless you know what you're doing.</div>
                    <div class="mb-4">
                        <label class="admin-form-label">Hero lead paragraph</label>
                        <textarea name="hero_lead" class="form-control" rows="2">{{ old('hero_lead', setting('home.hero_lead', 'Hot water extraction for area rugs, wall-to-wall carpet, sectionals, armchairs and mattresses. Stains, pet odors and dust mites gone in one visit — dry in 4-6 hours.')) }}</textarea>
                    </div>

                    <div class="admin-form-hint mb-4">Sections below are in page order: 1st is the featured picks at the top, 2nd is "How it works", 3rd is the leftover-services grid (only shows up on the page when a service is left over after featured).</div>

                    <hr>
                    <h3 class="admin-form-subheading">Section 1</h3>
                    <div class="mb-4"><label class="admin-form-label">Title</label><input type="text" name="section_1_title" class="form-control" value="{{ old('section_1_title', setting('home.section_1_title', 'Our most-requested work')) }}"></div>
                    <div class="mb-4"><label class="admin-form-label">Lead</label><textarea name="section_1_lead" class="form-control" rows="2">{{ old('section_1_lead', setting('home.section_1_lead', 'A mix of what people book us for most — from truck-grade carpet and upholstery extraction to a full home reset.')) }}</textarea></div>

                    <h3 class="admin-form-subheading">Section 2</h3>
                    <div class="mb-4"><label class="admin-form-label">Title</label><input type="text" name="section_2_title" class="form-control" value="{{ old('section_2_title', setting('home.section_2_title', 'How it works')) }}"></div>

                    <h3 class="admin-form-subheading">Section 3</h3>
                    <div class="mb-4"><label class="admin-form-label">Title</label><input type="text" name="section_3_title" class="form-control" value="{{ old('section_3_title', setting('home.section_3_title', 'Other services')) }}"></div>
                    <div class="mb-4"><label class="admin-form-label">Lead</label><textarea name="section_3_lead" class="form-control" rows="2">{{ old('section_3_lead', setting('home.section_3_lead', 'Carpet and upholstery cleaning is our main line of work, but the same crew can take care of the rest of the place too.')) }}</textarea></div>

                    <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    @endif

    @if ($group === 'services-page')
        <div class="admin-card">
            <div class="admin-card-header"><h2>Services page copy</h2></div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'services-page') }}">
                    @csrf @method('PUT')

                    <h3 class="admin-form-subheading">Hero</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="admin-form-label">Hero line 1</label>
                            <input type="text" name="hero_line_1" class="form-control" value="{{ old('hero_line_1', setting('services-page.hero_line_1', 'Our')) }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="admin-form-label">Hero line 2</label>
                            <input type="text" name="hero_line_2" class="form-control" value="{{ old('hero_line_2', setting('services-page.hero_line_2', '<img src="'.asset('img/ui/t8.jpg').'" alt="image" class="mil-text-image mil-long"> services')) }}">
                        </div>
                    </div>
                    <div class="admin-form-hint mb-4">Hero line 2 may contain the decorative inline image markup — leave the HTML alone unless you know what you're doing.</div>

                    <div class="admin-form-hint mb-4">Sections below are in page order: 1st is the full flat list of all active services (compact cards, no folder split), 2nd is the "How a visit works" steps.</div>

                    <hr>
                    <h3 class="admin-form-subheading">Section 1</h3>
                    <div class="mb-4"><label class="admin-form-label">Title</label><input type="text" name="section_1_title" class="form-control" value="{{ old('section_1_title', setting('services-page.section_1_title', 'All services')) }}"></div>
                    <div class="mb-4"><label class="admin-form-label">Lead</label><textarea name="section_1_lead" class="form-control" rows="2">{{ old('section_1_lead', setting('services-page.section_1_lead', "Everything we clean, in one list — carpets, upholstery, mattresses, and the rest of the home or office. Pick a card to see what's included.")) }}</textarea></div>

                    <h3 class="admin-form-subheading">Section 2</h3>
                    <div class="mb-4"><label class="admin-form-label">Title</label><input type="text" name="section_2_title" class="form-control" value="{{ old('section_2_title', setting('services-page.section_2_title', 'How a visit works')) }}"></div>

                    <h3 class="admin-form-subheading">CTA banner</h3>
                    <div class="mb-4"><label class="admin-form-label">Headline</label><input type="text" name="cta_headline" class="form-control" value="{{ old('cta_headline', setting('services-page.cta_headline', "Not sure which service you need? Send us a photo and we'll tell you.")) }}"></div>

                    <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    @endif

    @if ($group === 'contact-page')
        <div class="admin-card">
            <div class="admin-card-header"><h2>Contact page copy</h2></div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'contact-page') }}">
                    @csrf @method('PUT')

                    <h3 class="admin-form-subheading">Hero</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="admin-form-label">Hero line 1</label>
                            <input type="text" name="hero_line_1" class="form-control" value="{{ old('hero_line_1', setting('contact-page.hero_line_1', "Let's get in")) }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="admin-form-label">Hero line 2</label>
                            <input type="text" name="hero_line_2" class="form-control" value="{{ old('hero_line_2', setting('contact-page.hero_line_2', '<img src="'.asset('img/ui/t10.jpg').'" alt="image" class="mil-text-image mil-long"> touch')) }}">
                        </div>
                    </div>
                    <div class="admin-form-hint mb-4">Hero line 2 may contain the decorative inline image markup — leave the HTML alone unless you know what you're doing.</div>

                    <div class="mb-4">
                        <label class="admin-form-label">Form box heading</label>
                        <input type="text" name="form_heading" class="form-control" value="{{ old('form_heading', setting('contact-page.form_heading', 'We accept your requests <br>24 hours a day, 7 days a week')) }}">
                        <div class="admin-form-hint">A <code>&lt;br&gt;</code> controls the line wrap.</div>
                    </div>

                    <div class="mb-4"><label class="admin-form-label">Section 1 title</label><input type="text" name="section_1_title" class="form-control" value="{{ old('section_1_title', setting('contact-page.section_1_title', 'Write to us')) }}"></div>
                    <div class="mb-4"><label class="admin-form-label">Section 2 title</label><input type="text" name="section_2_title" class="form-control" value="{{ old('section_2_title', setting('contact-page.section_2_title', 'Contact info')) }}"></div>

                    <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    @endif

@endsection
