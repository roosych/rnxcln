# RYNEXClean

Marketing site for a Chicago carpet, rug and upholstery cleaning company.
Laravel 13 + Blade, PostgreSQL, served by nginx + PHP-FPM in Docker.

Ported from the static `cleanfix` HTML build: the five pages were split into a
layout, partials and reusable components, and the repeated content was lifted
into config files.

## Running it

```bash
docker compose up -d --build
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link

# First run only — pulls the existing config/*.php content into the database:
docker compose exec app php artisan content:import-config

# Create an admin (or editor) account for /admin:
docker compose exec app php artisan admin:create-user

npm install && npm run build
```

Site: <http://localhost:8080>
Admin panel: <http://localhost:8080/admin>
Postgres: `localhost:5433` (5433 on the host so it does not clash with a local 5432)

Override ports and credentials through the environment before `up`:

| Variable       | Default      |
| -------------- | ------------ |
| `APP_PORT`     | `8080`       |
| `DB_PORT_HOST` | `5433`       |
| `DB_DATABASE`  | `rynexclean` |
| `DB_USERNAME`  | `rynexclean` |
| `DB_PASSWORD`  | `secret`     |

Useful commands:

```bash
docker compose logs -f app
docker compose exec app php artisan view:clear
docker compose exec postgres psql -U rynexclean -d rynexclean
```

## Layout of the views

```
resources/views/
├── layouts/app.blade.php        base document; everything outside #swupMain
│                                stays put between swup page transitions
├── partials/                    head, preloader, header, call widget, footer, scripts
├── components/                  reusable blocks (see below)
└── pages/                       home, about, services, carpet-upholstery, contact
```

Components worth knowing:

| Component               | Used for                                                 |
| ----------------------- | -------------------------------------------------------- |
| `x-hero`                | inner-page hero: title lines, breadcrumbs, buttons slot   |
| `x-hero-home`           | the home page hero, which has a different shape           |
| `x-section-title`       | numbered heading + optional intro paragraph               |
| `x-service-long`        | wide service card with photo and check list               |
| `x-service-wide`        | service card with check list and a button                 |
| `x-service-icon`        | small icon card in a grid                                 |
| `x-step-card`           | numbered process step                                     |
| `x-faq` / `x-faq-item`  | accordion; `x-faq` splits a set into two columns          |
| `x-reviews`             | swiper testimonial slider                                 |
| `x-about-boxes`         | the "a few words about us" mosaic, shared by two pages    |
| `x-before-after`        | drag-to-compare sliders                                   |
| `x-cta`                 | call-to-action band                                       |
| `x-features`            | three-up icon tiles                                       |
| `x-check-list`          | tick list, `variant="spaced"` for the wide columns        |
| `x-star-burst`, `x-scroll-hint`, `x-user-avatars` | small decorative pieces         |

## Admin panel

`/admin` is a small, self-contained admin theme (`resources/views/layouts/admin.blade.php`,
`resources/css/admin.css`) — no admin framework, just Bootstrap 5 + the
public site's own FontAwesome build, colored with the site's real accent
palette (`.mil-a-1` / `.mil-a-2` / `.mil-m-*` in `public/css/style.css`).
Services, service categories, FAQ, process steps and Reviews support
drag-to-reorder (SortableJS, posts to a `{resource}/reorder` route). Every
`store`/`update` action is validated through a `FormRequest` class in
`app/Http/Requests[/Admin]`, one per form. It manages everything that used to
be edited by hand in the config files:

| Section          | Manages                                                          |
| ----------------- | ----------------------------------------------------------------- |
| Leads             | contact-form and call-back submissions, with a status workflow and CSV export |
| Services          | catalogue items grouped into categories (core service cards, "what we clean"/add-on icon grids, home & office wide cards) — title, text, checklist, image/icon, before/after photos and reserved SEO fields |
| Service categories | the categories above: name, layout type, order, active flag      |
| FAQ               | the three FAQ sets (home/services/carpet)                         |
| Process steps     | the "how it works" step cards on the home, services and carpet pages |
| Reviews           | testimonials                                                      |
| Site settings     | contacts, hours, socials, navigation, service-area ZIPs, the "About" page copy, the carpet page's checklist columns, contact-form service types |
| SEO               | per-page title/description/canonical/robots, GA4 & Yandex Metrika IDs, robots.txt |
| Users             | admin/editor accounts (`admin` role only)                         |

Two roles: `admin` (everything) and `editor` (leads + content, no Settings/
SEO/Users). Create accounts with `php artisan admin:create-user` — there is
no public sign-up route.

`config/site.php` and `config/catalog.php` are no longer read at runtime;
`php artisan content:import-config` was a one-time seed into the `settings`,
`services`/`service_categories`, `faq_items`, `process_steps` and `reviews`
tables, which the admin panel now owns. The config files are kept as
historical reference only.

## Where the content lives

| Table               | Holds                                                            |
| -------------------- | ------------------------------------------------------------------ |
| `settings`           | company name, phone, email, address, hours, nav, footer, socials, ZIP list, About-page copy, carpet-page checklist columns — grouped `site.*` / `catalog.*` / `about.*` |
| `service_categories` | the groups a service item belongs to (layout type: long card / icon / wide card) |
| `services`           | every service item (core cards, "what we clean", add-ons, home & office), tagged with a `category_id` |
| `faq_items`          | the three FAQ sets                                                |
| `process_steps`      | the "how it works" step cards, grouped by page                    |
| `reviews`            | testimonials                                                      |
| `page_seo`           | per-route meta title/description/canonical/robots/schema override |

All of the above are edited through `/admin`, not by hand. Read them from a
Blade view with the `setting('group.key')` helper (`App\Support\helpers.php`)
or the matching Eloquent model.

### The logo

`public/img/ui/logo2.png` puts the wordmark in the lower half of the frame, with
the sparkles filling the top. Centring the raw image box therefore drops the text
below whatever sits next to it, so each placement gets a corrective lift, kept in
`config/site.logo.lift`: `0.85rem` in the header and preloader (flex-centred),
`1.5rem` in the footer (top-aligned against the link column). `logo2-light.png`
is the white version used on the dark footer.

## Forms

Both the contact form and the call-back widget post to `LeadController` and write
to the `leads` table, tagged by `source`. They show up in `/admin/leads` with a
`new` → `contacted` → `booked` → `closed` status. Nothing is emailed yet — wire up
a Mailable or a notification when the destination inbox is known.

## Known gaps

- `public/img/gallery/ba-*.jpg` are placeholder before/after pairs; replace with
  real job photos, matching proportions within each pair.
- No favicon: `public/img/ui/favicon.png` still belongs to the original template,
  so the tag is commented out in `partials/head.blade.php`.
- Contact details were unified on the ones from the old `contact.html`
  (`+1 (224) 310-2110`, Lincolnshire). The header and footer of the static build
  used a `555` placeholder instead.
