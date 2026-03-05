<?php

namespace App\Http\Controllers;

use App\Models\KnownIngredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Search known ingredients for autocomplete.
     * GET /api/ingredients?q=flour
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $ingredients = KnownIngredient::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'brand']);

        return response()->json($ingredients);
    }
}
