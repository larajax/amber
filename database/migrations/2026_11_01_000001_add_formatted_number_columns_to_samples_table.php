<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding the sample columns the formatted number examples edit.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->unsignedBigInteger('units_sold')->nullable();
            $table->decimal('export_price', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn(['annual_revenue', 'units_sold', 'export_price']);
        });
    }
};
