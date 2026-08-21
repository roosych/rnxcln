<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutSettingsRequest;
use App\Http\Requests\Admin\CompanySettingsRequest;
use App\Http\Requests\Admin\ContactPageSettingsRequest;
use App\Http\Requests\Admin\HomeSettingsRequest;
use App\Http\Requests\Admin\HoursSettingsRequest;
use App\Http\Requests\Admin\ServicesPageSettingsRequest;
use App\Http\Requests\Admin\SocialsSettingsRequest;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public const GROUPS = [
        'company', 'hours', 'socials',
        'home', 'services-page', 'contact-page', 'about',
    ];

    private const LOGO_FIELDS = ['logo_dark', 'logo_light', 'favicon'];

    public function edit(string $group = 'company'): View
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        return view('admin.settings.index', [
            'group' => $group,
            'groups' => self::GROUPS,
        ]);
    }

    public function update(string $group): RedirectResponse
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        match ($group) {
            'company' => $this->updateCompany(app(CompanySettingsRequest::class)),
            'hours' => $this->updateLines(app(HoursSettingsRequest::class), 'site', 'hours'),
            'socials' => $this->updateSocials(app(SocialsSettingsRequest::class)),
            'home' => $this->updateSimpleGroup(app(HomeSettingsRequest::class), 'home'),
            'services-page' => $this->updateSimpleGroup(app(ServicesPageSettingsRequest::class), 'services-page'),
            'contact-page' => $this->updateSimpleGroup(app(ContactPageSettingsRequest::class), 'contact-page'),
            'about' => $this->updateSimpleGroup(app(AboutSettingsRequest::class), 'about'),
        };

        return redirect()->route('admin.settings.index', $group)->with('status', 'Settings saved.');
    }

    private function updateCompany(CompanySettingsRequest $request): void
    {
        $data = $request->validated();

        Setting::put('site', 'name', $data['name']);
        Setting::put('site', 'phone', $data['phone']);
        Setting::put('site', 'phone_e164', $data['phone_e164']);
        Setting::put('site', 'email', $data['email']);
        Setting::put('site', 'address', [
            'city' => $data['address_city'],
            'line_1' => $data['address_line_1'],
            'line_2' => $data['address_line_2'],
        ]);
        Setting::put('site', 'stats', [
            'jobs' => (int) $data['stats_jobs'],
            'years' => (int) $data['stats_years'],
            'since' => (int) $data['stats_since'],
            'rating' => $data['stats_rating'],
        ]);
        Setting::put('site', 'footer_seo_text', $data['footer_seo_text']);

        foreach (self::LOGO_FIELDS as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $old = Setting::get('site', $field);
            if ($old) {
                Storage::disk('public')->delete($old);
            }

            Setting::put('site', $field, $request->file($field)->store('branding', 'public'));
        }
    }

    private function updateLines(HoursSettingsRequest $request, string $group, string $key): void
    {
        Setting::put($group, $key, $this->linesToArray($request->validated('lines')));
    }

    private function updateSocials(SocialsSettingsRequest $request): void
    {
        $socials = $this->linesToArray($request->validated('lines'))
            ->map(function ($line, $index) {
                $parts = array_map('trim', explode('|', $line, 2));

                throw_if(count($parts) !== 2, ValidationException::withMessages([
                    'lines' => 'Line '.($index + 1).' must be "icon class|url".',
                ]));

                return ['icon' => $parts[0], 'url' => $parts[1]];
            })
            ->values()
            ->all();

        Setting::put('site', 'socials', $socials);
    }

    private function updateSimpleGroup(FormRequest $request, string $group): void
    {
        foreach ($request->validated() as $key => $value) {
            Setting::put($group, $key, $value);
        }
    }

    private function linesToArray(?string $text)
    {
        return collect(explode("\n", (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values();
    }
}
