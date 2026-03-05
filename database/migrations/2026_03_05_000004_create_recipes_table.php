<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Max ~300 chars, enforced in validation
            $table->unsignedSmallInteger('prep_time_minutes')->nullable();
            $table->unsignedSmallInteger('cook_time_minutes')->nullable();
            $table->unsignedSmallInteger('rest_time_minutes')->nullable();
            $table->unsignedSmallInteger('total_time_minutes')->nullable();
            $table->unsignedSmallInteger('servings')->default(1);
            $table->string('yield')->nullable(); // e.g. "12 cookies", "1 loaf"
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('image_path')->nullable();
            $table->enum('status', ['draft', 'published', 'flagged', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->fullText(['title', 'description']);
        });

        Schema::create('category_recipe', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'recipe_id']);
        });

        Schema::create('recipe_tag', function (Blueprint $table) {
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['recipe_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_tag');
        Schema::dropIfExists('category_recipe');
        Schema::dropIfExists('recipes');
    }
};
