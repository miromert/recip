<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BasketController extends Controller
{
    public function index(): View
    {
        return view('basket.index');
    }

    public function ingredients(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipe_ids' => ['required', 'array', 'max:50'],
            'recipe_ids.*' => ['integer'],
        ]);

        $recipeIds = $validated['recipe_ids'];

        // Only include published recipes
        $recipes = Recipe::whereIn('id', $recipeIds)
            ->where('status', 'published')
            ->select('id', 'title', 'slug')
            ->get();

        $validIds = $recipes->pluck('id')->toArray();

        $ingredients = Ingredient::whereIn('recipe_id', $validIds)
            ->get();

        // Group and sum ingredients
        $grouped = [];
        foreach ($ingredients as $ingredient) {
            if ($ingredient->known_ingredient_id && $ingredient->unit) {
                $key = 'ki:' . $ingredient->known_ingredient_id . ':' . strtolower($ingredient->unit);
            } else {
                $key = 'name:' . strtolower(trim($ingredient->name)) . ':' . strtolower($ingredient->unit ?? '');
            }

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'name' => $ingredient->name,
                    'amount' => 0,
                    'unit' => $ingredient->unit,
                ];
            }

            $grouped[$key]['amount'] += (float) $ingredient->amount;
        }

        // Sort alphabetically by name
        $items = array_values($grouped);
        usort($items, fn($a, $b) => strcasecmp($a['name'], $b['name']));

        // Clean up amounts: remove trailing zeros
        foreach ($items as &$item) {
            if ($item['amount'] == (int) $item['amount']) {
                $item['amount'] = (int) $item['amount'];
            } else {
                $item['amount'] = round($item['amount'], 2);
            }
        }

        return response()->json([
            'recipes' => $recipes,
            'ingredients' => $items,
        ]);
    }
}
