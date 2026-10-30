<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, adding the relations the relation, record finder, nested form and tag list reference pages edit.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('primary_group_id')->nullable()->constrained('user_groups')->nullOnDelete();
            $table->foreignId('primary_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('group_code')->nullable();
        });

        Schema::create('category_sample', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sample_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'sample_id']);
        });

        Schema::create('sample_tag', function (Blueprint $table) {
            $table->foreignId('sample_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['sample_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_tag');
        Schema::dropIfExists('category_sample');

        Schema::table('samples', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewer_id');
            $table->dropConstrainedForeignId('primary_group_id');
            $table->dropConstrainedForeignId('primary_category_id');
            $table->dropColumn('group_code');
        });
    }
};
