<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2)->nullable(); // e.g. 1.50
            $table->string('unit', 20)->nullable(); // g, ml, cup, tbsp, tsp, oz, lb, etc.
            $table->string('name'); // "all-purpose flour"
            $table->string('group')->nullable(); // "For the sauce", "For the dough"
            $table->unsignedSmallInteger('order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
