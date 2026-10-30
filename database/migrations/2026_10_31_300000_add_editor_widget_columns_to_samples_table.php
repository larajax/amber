<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding a sample column for each example on the editor and input form widget pages.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            // Code editor
            $table->text('php_code')->nullable();
            $table->text('config_code')->nullable();
            $table->text('script_code')->nullable();
            $table->text('scss_code')->nullable();
            $table->text('generated_code')->nullable();
            $table->text('embed_code')->nullable();

            // Color picker
            $table->string('text_color')->nullable();
            $table->string('brand_color')->nullable();
            $table->string('border_color')->nullable();

            // Data table
            $table->text('deliveries')->nullable();
            $table->text('variants')->nullable();
            $table->text('stockists')->nullable();
            $table->text('parts')->nullable();
            $table->text('price_breaks')->nullable();
            $table->text('bundle_items')->nullable();
            $table->text('suppliers')->nullable();
            $table->text('components')->nullable();
            $table->text('stock_levels')->nullable();
            $table->text('reviews')->nullable();
            $table->text('spec_sheet')->nullable();
            $table->text('certifications')->nullable();
            $table->text('awards')->nullable();

            // Date picker
            $table->time('closes_at')->nullable();
            $table->date('restock_on')->nullable();
            $table->date('launch_on')->nullable();
            $table->date('delivery_on')->nullable();
            $table->date('manufactured_on')->nullable();
            $table->date('review_on')->nullable();
            $table->date('discontinued_on')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('webinar_at')->nullable();
            $table->dateTime('inspected_at')->nullable();
            $table->dateTime('sold_at')->nullable();

            // Markdown editor
            $table->text('changelog')->nullable();
            $table->text('faq')->nullable();
            $table->text('announcement')->nullable();
            $table->text('terms')->nullable();
            $table->text('warranty')->nullable();
            $table->text('care_guide')->nullable();

            // Rich editor
            $table->text('overview')->nullable();
            $table->text('key_points')->nullable();
            $table->text('specifications')->nullable();
            $table->text('story')->nullable();
            $table->text('signature')->nullable();
            $table->text('article')->nullable();
            $table->text('teaser')->nullable();
            $table->text('disclaimer')->nullable();
            $table->text('press_release')->nullable();

            // Sensitive
            $table->string('webhook_secret')->nullable();
            $table->string('access_token')->nullable();
            $table->string('license_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn([
                'php_code', 'config_code', 'script_code', 'scss_code', 'generated_code', 'embed_code',
                'text_color', 'brand_color', 'border_color',
                'deliveries', 'variants', 'stockists', 'parts', 'price_breaks', 'bundle_items', 'suppliers',
                'components', 'stock_levels', 'reviews', 'spec_sheet', 'certifications', 'awards',
                'closes_at', 'restock_on', 'launch_on', 'delivery_on', 'manufactured_on', 'review_on',
                'discontinued_on', 'expires_at', 'webinar_at', 'inspected_at', 'sold_at',
                'changelog', 'faq', 'announcement', 'terms', 'warranty', 'care_guide',
                'overview', 'key_points', 'specifications', 'story', 'signature', 'article', 'teaser',
                'disclaimer', 'press_release',
                'webhook_secret', 'access_token', 'license_key',
            ]);
        });
    }
};
