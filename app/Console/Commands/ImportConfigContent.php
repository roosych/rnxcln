<?php

namespace App\Console\Commands;

use App\Models\FaqItem;
use App\Models\PageSeo;
use App\Models\ProcessStep;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('content:import-config {--force : Wipe and re-import even if rows already exist}')]
#[Description('One-time import of config/site.php, catalog.php, faq.php and reviews.php into the database')]
class ImportConfigContent extends Command
{
    /**
     * service_areas and the Home/Services "how it works" process_steps
     * aren't imported here — both are seeded once directly by their
     * `create_*_table` migration instead (site.service_zips can't be read
     * from the settings table at `migrate` time, since this command hasn't
     * run yet), so re-running this with --force doesn't touch either.
     *
     * page_seos isn't part of the "already imported?" check or the --force
     * wipe either: the `add_legal_pages_seo` migration inserts 3 rows there
     * (privacy-policy, terms, cookie-policy) before this command ever runs,
     * which would otherwise make a completely fresh install look
     * "already imported" and refuse to run at all. importPageSeo() uses
     * updateOrCreate() instead, so it's safe to (re-)run without wiping —
     * the migration-owned legal-page rows are simply left alone.
     */
    public function handle(): int
    {
        $force = (bool) $this->option('force');

        if (! $force && (Service::count() || FaqItem::count() || Review::count() || Setting::count())) {
            $this->error('Content already imported. Re-run with --force to wipe and re-import.');

            return self::FAILURE;
        }

        if ($force) {
            Service::query()->delete();
            FaqItem::query()->delete();
            Review::query()->delete();
            Setting::query()->delete();
        }

        $this->importServices();
        $this->importFaq();
        $this->importReviews();
        $this->importSiteSettings();
        $this->importPageSeo();

        $this->info('Content imported.');

        return self::SUCCESS;
    }

    /**
     * No more service_categories table — `section` is a plain string on
     * Service itself, set here directly and never edited through the admin
     * (Home/Services decide what to render by querying for it — see
     * App\Models\Service::scopeSection()). The old "what we clean" icon grid
     * isn't imported any more: it duplicated the checklist that already
     * lives on the core services below, now visible on each one's own page.
     * `link_type` only knows page/contact/custom now: every service has its
     * own page, so what used to be 'carpet_detail' or 'none' both just mean
     * "link to this service's own page" (the default).
     */
    private function importServices(): void
    {
        foreach (config('catalog.core') as $i => $item) {
            $service = Service::create([
                'section' => 'core',
                'title' => $item['title'],
                'slug' => Str::slug($item['title']),
                'tagline' => $item['tagline'] ?? null,
                'text' => $item['text'] ?? null,
                'image' => $item['image'] ?? null,
                'alt' => $item['alt'] ?? null,
                'items' => $item['items'] ?? [],
                'before_image' => $item['before_image'] ?? null,
                'after_image' => $item['after_image'] ?? null,
                'sort_order' => $i,
                // The homepage's featured section shows these three by
                // default until an admin curates the top-picks list.
                'is_featured' => true,
            ]);

            foreach ($item['steps'] ?? [] as $j => $step) {
                ProcessStep::create([
                    'service_id' => $service->id,
                    'title' => $step['title'],
                    'text' => $step['text'] ?? '',
                    'sort_order' => $j,
                ]);
            }
        }

        foreach (config('catalog.home_office') as $i => $item) {
            Service::create([
                'section' => 'home-office',
                'title' => $item['title'],
                'slug' => Str::slug('home-office-'.strip_tags($item['title'])),
                'items' => $item['items'] ?? [],
                'sort_order' => $i,
            ]);
        }
    }

    /**
     * One universal FAQ now (shown on Home, Services, and every service
     * page) instead of a separate set per page — config/faq.php still has
     * the old three groups with a lot of near-duplicate questions between
     * them, so this only imports the 'home' set as a starting point rather
     * than flattening all three back into duplicates. Add the rest through
     * /admin/faq afterwards.
     */
    private function importFaq(): void
    {
        foreach (config('faq.home', []) as $i => $item) {
            FaqItem::create([
                'question' => $item['q'],
                'answer' => $item['a'],
                'sort_order' => $i,
            ]);
        }
    }

    private function importReviews(): void
    {
        foreach (config('reviews') as $i => $review) {
            Review::create([
                'name' => $review['name'],
                'location' => $review['location'] ?? null,
                'text' => $review['text'],
                'sort_order' => $i,
            ]);
        }
    }

    private function importSiteSettings(): void
    {
        foreach ([
            'name', 'phone', 'phone_e164', 'email', 'address', 'hours', 'stats', 'socials',
        ] as $key) {
            Setting::put('site', $key, config("site.{$key}"));
        }
    }

    private function importPageSeo(): void
    {
        $defaults = [
            'home' => [
                'meta_title' => 'Carpet, Rug & Upholstery Cleaning in Chicago',
                'meta_description' => 'Deep cleaning for carpets, area rugs, sofas, armchairs and mattresses across Chicago. Hot water extraction, pet stain and odor removal, dry in 4-6 hours.',
            ],
            'about' => [
                'meta_title' => 'About Us',
                'meta_description' => 'Who we are: a Chicago carpet, rug and upholstery cleaning crew working across the city and near suburbs since 2022.',
            ],
            'services' => [
                'meta_title' => 'Services',
                'meta_description' => 'Everything we clean in Chicago: carpets, area rugs, sofas, armchairs, mattresses and curtains, plus home, office, move-out and Airbnb turnover cleaning.',
            ],
            'contact' => [
                'meta_title' => 'Contact',
                'meta_description' => 'Book carpet, rug, sofa and mattress cleaning in Chicago. Requests accepted 24/7, we reply within one business hour.',
            ],
        ];

        foreach ($defaults as $routeName => $meta) {
            PageSeo::updateOrCreate(['route_name' => $routeName], $meta);
        }
    }
}
