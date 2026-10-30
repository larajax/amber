<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, creating the sample links the repeater stores as related records.
     */
    public function up(): void
    {
        Schema::create('sample_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_links');
    }
};
