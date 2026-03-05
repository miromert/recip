<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Toggle vote on a recipe.
     * POST /recipes/{recipe}/vote
     */
    public function toggle(Recipe $recipe): JsonResponse
    {
        $user = Auth::user();

        $existing = Vote::where('user_id', $user->id)
            ->where('recipe_id', $recipe->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $voted = false;
        } else {
            Vote::create([
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
            ]);
            $voted = true;
        }

        return response()->json([
            'voted' => $voted,
            'count' => $recipe->votes()->count(),
        ]);
    }
}
