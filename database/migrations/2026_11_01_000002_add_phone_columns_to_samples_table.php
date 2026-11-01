<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding the sample columns the phone examples edit.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->string('mobile', 32)->nullable();
            $table->string('office_phone', 32)->nullable();
            $table->string('support_phone', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'office_phone', 'support_phone']);
        });
    }
};
