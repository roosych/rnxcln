<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_steps', function (Blueprint $table) {
            $table->id();
            // Page-level steps (Home's "How it works", Services' "How a visit
            // works") carry a group and no service_id. A service's own "How we
            // clean it" steps carry a service_id and no group instead — see
            // App\Http\Controllers\Admin\ProcessStepController's class docblock.
            $table->string('group', 20)->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('title', 200);
            $table->text('text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $this->seed();
    }

    public function down(): void
    {
        Schema::dropIfExists('process_steps');
    }

    /**
     * One-time seed of the two page-level step lists — nothing else creates
     * these (unlike per-service steps, which come from config/catalog.php via
     * ImportConfigContent), so this is the only place they exist.
     */
    private function seed(): void
    {
        $now = now();

        $steps = [
            'home' => [
                ['title' => 'Send your <br>request', 'text' => 'Message us with what needs cleaning and your ZIP code. We reply with a fixed quote and a booking window, usually within one business hour.'],
                ['title' => 'We arrive <br>and inspect', 'text' => 'Our crew arrives in the window we agreed on, identifies the fiber and tests for colorfastness in a hidden spot before starting.'],
                ['title' => 'Hot water <br>extraction', 'text' => 'Heated solution goes in, dirt and residue come straight back out — cushions, seams and all.'],
                ['title' => 'Dry and <br>ready', 'text' => 'Pile is groomed, air movers go on, and we check moisture before we leave. Dry in 4-6 hours.'],
            ],
            'services' => [
                ['title' => 'Tell us what <br>needs cleaning', 'text' => 'Send a photo or list the items. You get a fixed quote and a booking window, not a vague morning slot.'],
                ['title' => 'Fabric test <br>and inspection', 'text' => 'On arrival we identify the fiber, check the care tag and test for colorfastness in a hidden spot.'],
                ['title' => 'Deep clean <br>and extraction', 'text' => 'HEPA vacuum, pre-spray, hot water extraction and a neutralizing rinse — cushions, seams and edges included.'],
                ['title' => 'Grooming <br>and fast drying', 'text' => 'Pile is groomed, air movers go on, and we check moisture before we pack up. Dry in 4-6 hours.'],
            ],
        ];

        foreach ($steps as $group => $items) {
            foreach ($items as $i => $item) {
                DB::table('process_steps')->insert($item + [
                    'group' => $group,
                    'sort_order' => $i,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
};
