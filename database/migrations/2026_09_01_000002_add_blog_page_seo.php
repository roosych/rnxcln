<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| Same pattern as 2026_08_20_080453_add_legal_pages_seo.php: gives /blog a
| PageSeo row so its title/description are editable from Admin > SEO and
| it's picked up by the sitemap query (PageSeo::where('robots', 'index')).
| Category and single-post pages get their SEO straight from the Service /
| BlogPost record instead — see resources/views/partials/head.blade.php.
*/
return new class extends Migration
{
    public function up(): void
    {
        DB::table('page_seos')->insert([
            'route_name' => 'blog.index',
            'meta_title' => 'Blog',
            'meta_description' => 'Cleaning tips, guides and news from RYNEXClean.',
            'robots' => 'index',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('page_seos')->where('route_name', 'blog.index')->delete();
    }
};
