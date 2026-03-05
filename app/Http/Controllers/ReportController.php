<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Store a report for a recipe.
     */
    public function store(Request $request, Recipe $recipe): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        // Prevent duplicate reports from same user
        $existing = Report::where('user_id', Auth::id())
            ->where('recipe_id', $recipe->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('info', 'You have already reported this recipe.');
        }

        Report::create([
            'user_id' => Auth::id(),
            'recipe_id' => $recipe->id,
            'reason' => $validated['reason'],
        ]);

        // Auto-flag recipe if it has 5+ pending reports
        $pendingCount = Report::where('recipe_id', $recipe->id)
            ->where('status', 'pending')
            ->count();

        if ($pendingCount >= 5 && $recipe->status === 'published') {
            $recipe->update(['status' => 'flagged']);
        }

        return back()->with('success', 'Thank you for your report. We will review it.');
    }
}
