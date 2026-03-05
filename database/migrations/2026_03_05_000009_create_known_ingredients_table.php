<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Standardized ingredient library
        Schema::create('known_ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                  // "bread flour", "olive oil"
            $table->string('slug')->unique();                  // "bread-flour", "olive-oil"
            $table->string('brand')->nullable();               // "King Arthur", optional
            $table->decimal('calories_per_100g', 7, 2)->nullable(); // future: nutrition tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Link recipe ingredients to the known ingredient library (optional FK)
        Schema::table('ingredients', function (Blueprint $table) {
            $table->foreignId('known_ingredient_id')->nullable()->after('recipe_id')
                  ->constrained('known_ingredients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('known_ingredient_id');
        });

        Schema::dropIfExists('known_ingredients');
    }
};
