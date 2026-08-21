<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| New static pages behind the footer's "legal" links, which used to all
| point at /contact as a placeholder. Content is a "coming soon" stub for
| now (see resources/views/pages/legal.blade.php) — robots is 'noindex'
| until real content is written in; flip it to 'index' from /admin/seo
| once it is.
*/
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('page_seos')->insert([
            [
                'route_name' => 'privacy-policy',
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'How RYNEXClean collects, uses and protects your information.',
                'robots' => 'noindex',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'route_name' => 'terms-and-conditions',
                'meta_title' => 'Terms and Conditions',
                'meta_description' => 'The terms that apply when you book or use RYNEXClean services.',
                'robots' => 'noindex',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'route_name' => 'cookie-policy',
                'meta_title' => 'Cookie Policy',
                'meta_description' => 'How RYNEXClean uses cookies on this site.',
                'robots' => 'noindex',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('page_seos')->whereIn('route_name', ['privacy-policy', 'terms-and-conditions', 'cookie-policy'])->delete();
    }
};
