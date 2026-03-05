<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $recipes = collect();

        if (strlen($query) >= 2) {
            $recipes = Recipe::published()
                ->with(['user', 'categories'])
                ->withCount('votes')
                ->whereFullText(['title', 'description'], $query)
                ->paginate(12)
                ->withQueryString();
        }

        return view('search.index', [
            'query' => $query,
            'recipes' => $recipes,
        ]);
    }
}
