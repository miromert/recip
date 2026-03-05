<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\KnownIngredient;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Step;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@skiptorecipe.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create categories
        $categories = collect([
            'Appetizers', 'Breakfast', 'Main Course', 'Dessert', 'Salads',
            'Soups', 'Sides', 'Drinks', 'Snacks', 'Baking', 'Sauces',
            'Vegetarian', 'Vegan',
        ])->map(fn($name) => Category::create(['name' => $name]));

        // Create a sample recipe
        $recipe = $admin->recipes()->create([
            'title' => 'Classic Margherita Pizza',
            'description' => 'Simple, authentic Neapolitan pizza with fresh mozzarella, basil, and San Marzano tomatoes.',
            'prep_time_minutes' => 30,
            'cook_time_minutes' => 12,
            'rest_time_minutes' => 60,
            'servings' => 4,
            'yield' => '2 pizzas',
            'difficulty' => 'medium',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Ingredients
        $ingredients = [
            ['amount' => 500, 'unit' => 'g', 'name' => 'bread flour', 'group' => 'Dough', 'order' => 0],
            ['amount' => 325, 'unit' => 'ml', 'name' => 'warm water', 'group' => 'Dough', 'order' => 1],
            ['amount' => 7, 'unit' => 'g', 'name' => 'instant yeast', 'group' => 'Dough', 'order' => 2],
            ['amount' => 10, 'unit' => 'g', 'name' => 'salt', 'group' => 'Dough', 'order' => 3],
            ['amount' => 15, 'unit' => 'ml', 'name' => 'olive oil', 'group' => 'Dough', 'order' => 4],
            ['amount' => 400, 'unit' => 'g', 'name' => 'San Marzano tomatoes (crushed)', 'group' => 'Topping', 'order' => 5],
            ['amount' => 250, 'unit' => 'g', 'name' => 'fresh mozzarella', 'group' => 'Topping', 'order' => 6],
            ['amount' => null, 'unit' => null, 'name' => 'fresh basil leaves', 'group' => 'Topping', 'order' => 7],
            ['amount' => null, 'unit' => 'pinch', 'name' => 'salt', 'group' => 'Topping', 'order' => 8],
        ];

        foreach ($ingredients as $ingredient) {
            // Create or find in the known ingredients library
            $slug = \Illuminate\Support\Str::slug($ingredient['name']);
            $known = KnownIngredient::firstOrCreate(
                ['slug' => $slug],
                ['name' => $ingredient['name'], 'created_by' => $admin->id]
            );
            $ingredient['known_ingredient_id'] = $known->id;
            $recipe->ingredients()->create($ingredient);
        }

        // Steps
        $steps = [
            'Mix flour, yeast, and salt in a large bowl. Add warm water and olive oil. Mix until a shaggy dough forms.',
            'Knead on a floured surface for 8-10 minutes until smooth and elastic.',
            'Place in an oiled bowl, cover with a damp towel, and let rise for 1 hour (or until doubled).',
            'Preheat your oven to the highest setting (250°C / 480°F) with a pizza stone or inverted baking sheet inside.',
            'Divide dough in half. Stretch each piece into a thin round on a floured surface. Do not use a rolling pin.',
            'Spread crushed tomatoes over the dough, leaving a 2cm border. Tear mozzarella over the top.',
            'Slide onto the hot stone/sheet. Bake for 10-12 minutes until the crust is golden and cheese is bubbling.',
            'Remove from oven, top with fresh basil leaves, drizzle with olive oil, and serve immediately.',
        ];

        foreach ($steps as $index => $instruction) {
            $recipe->steps()->create([
                'instruction' => $instruction,
                'order' => $index,
            ]);
        }

        // Attach categories
        $recipe->categories()->attach(
            $categories->filter(fn($c) => in_array($c->name, ['Main Course', 'Vegetarian']))->pluck('id')
        );
    }
}
