<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding the page link stored as a reference:// link for the URLs and Timezones reference page.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->string('page_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn('page_link');
        });
    }
};
