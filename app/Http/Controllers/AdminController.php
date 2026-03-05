<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Admin dashboard.
     */
    public function index(): View
    {
        return view('admin.index', [
            'totalRecipes' => Recipe::count(),
            'totalUsers' => User::count(),
            'totalPublished' => Recipe::where('status', 'published')->count(),
            'flaggedRecipes' => Recipe::where('status', 'flagged')->withCount('reports')->get(),
            'pendingReports' => Report::where('status', 'pending')
                ->with(['user', 'recipe'])
                ->latest()
                ->take(50)
                ->get(),
        ]);
    }

    /**
     * Dismiss a report.
     */
    public function dismissReport(Report $report): RedirectResponse
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('success', 'Report dismissed.');
    }

    /**
     * Mark a report as reviewed and archive the recipe.
     */
    public function archiveRecipe(Recipe $recipe): RedirectResponse
    {
        $recipe->update(['status' => 'archived']);
        $recipe->reports()->where('status', 'pending')->update(['status' => 'reviewed']);

        return back()->with('success', 'Recipe archived and reports marked as reviewed.');
    }

    /**
     * Restore a flagged/archived recipe to published.
     */
    public function restoreRecipe(Recipe $recipe): RedirectResponse
    {
        $recipe->update(['status' => 'published']);
        $recipe->reports()->where('status', 'pending')->update(['status' => 'dismissed']);

        return back()->with('success', 'Recipe restored.');
    }
}
