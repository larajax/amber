<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, creating the kitchen sink samples table and its pivot to user groups.
     */
    public function up(): void
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Text
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('code')->nullable();
            $table->string('reference')->nullable();
            $table->string('email')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->text('body')->nullable();
            $table->text('notes')->nullable();

            // Choices
            $table->string('status')->default('draft');
            $table->string('priority')->nullable();
            $table->string('category')->nullable();
            $table->string('icon')->nullable();
            $table->string('level')->nullable();
            $table->string('timezone')->nullable();
            $table->string('size')->nullable();
            $table->string('plan')->nullable();
            $table->text('features')->nullable();
            $table->text('permissions')->nullable();
            $table->text('interests')->nullable();
            $table->boolean('is_featured')->default(false);

            // Dates and colors
            $table->dateTime('published_at')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->time('opens_at')->nullable();
            $table->time('reminder_at')->nullable();
            $table->string('color')->nullable();
            $table->string('accent_color')->nullable();
            $table->string('overlay_color')->nullable();

            // Editors
            $table->text('css_code')->nullable();
            $table->text('html_code')->nullable();
            $table->text('template')->nullable();
            $table->text('readme')->nullable();
            $table->string('api_key')->nullable();
            $table->text('private_notes')->nullable();

            // Tags
            $table->text('tags')->nullable();
            $table->string('keywords')->nullable();
            $table->text('topics')->nullable();

            // Structured
            $table->text('content')->nullable();
            $table->text('steps')->nullable();
            $table->string('step_style')->nullable();
            $table->text('address')->nullable();
            $table->text('line_items')->nullable();
            $table->text('meta')->nullable();

            // Behaviour
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->boolean('has_discount')->default(false);
            $table->string('discount_code')->nullable();
            $table->integer('discount_percent')->nullable();
            $table->string('delivery')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('headline')->nullable();

            // Custom widget
            $table->dateTime('checked_at')->nullable();

            $table->timestamps();
        });

        Schema::create('sample_user_group', function (Blueprint $table) {
            $table->foreignId('sample_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_group_id')->constrained()->cascadeOnDelete();
            $table->primary(['sample_id', 'user_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_user_group');
        Schema::dropIfExists('samples');
    }
};
