<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding the sample columns the field type, layout and behaviour reference pages edit.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            // Choices
            $table->string('currency')->nullable();
            $table->string('stock_status')->nullable();
            $table->string('transport')->nullable();
            $table->string('visibility')->nullable();
            $table->string('audience')->nullable();

            // Structured
            $table->text('seo')->nullable();
            $table->text('packaging')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'stock_status',
                'transport',
                'visibility',
                'audience',
                'seo',
                'packaging',
            ]);
        });
    }
};
