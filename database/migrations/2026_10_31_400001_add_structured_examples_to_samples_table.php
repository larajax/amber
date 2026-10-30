<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding the JSON columns the nested form, repeater and tag list reference pages store.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->text('shipping')->nullable();
            $table->text('contact')->nullable();
            $table->text('faqs')->nullable();
            $table->text('highlights')->nullable();
            $table->text('milestones')->nullable();
            $table->text('dimensions')->nullable();
            $table->text('sections')->nullable();
            $table->text('specs')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('languages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn([
                'shipping',
                'contact',
                'faqs',
                'highlights',
                'milestones',
                'dimensions',
                'sections',
                'specs',
                'itinerary',
                'languages',
            ]);
        });
    }
};
