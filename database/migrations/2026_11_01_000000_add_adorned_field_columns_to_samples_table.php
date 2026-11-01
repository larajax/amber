<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding the sample columns the adorned field examples edit.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->decimal('shipping_fee', 10, 2)->nullable();
            $table->string('support_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn(['shipping_fee', 'support_email']);
        });
    }
};
