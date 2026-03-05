<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KnownIngredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RecipeController extends Controller
{
    /**
     * Homepage — list published recipes.
     */
    public function index(Request $request): View
    {
        $query = Recipe::published()->with(['user', 'categories'])->withCount('votes');

        // Sort
        $sort = $request->get('sort', 'recent');
        if ($sort === 'popular') {
            $query->orderByDesc('votes_count');
        } else {
            $query->orderByDesc('published_at');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->get('category'));
            });
        }

        $recipes = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('recipes.index', [
            'recipes' => $recipes,
            'categories' => $categories,
            'currentSort' => $sort,
            'currentCategory' => $request->get('category'),
        ]);
    }

    /**
     * Show a single recipe.
     */
    public function show(Recipe $recipe): View
    {
        // Only show published recipes (or own drafts)
        if ($recipe->status !== 'published') {
            if (!Auth::check() || Auth::id() !== $recipe->user_id) {
                abort(404);
            }
        }

        $recipe->load(['user', 'ingredients', 'steps', 'categories', 'tags']);
        $recipe->loadCount('votes');

        $hasVoted = Auth::check() ? Auth::user()->hasVotedFor($recipe) : false;

        return view('recipes.show', [
            'recipe' => $recipe,
            'hasVoted' => $hasVoted,
        ]);
    }

    /**
     * Show the recipe creation form.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('recipes.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new recipe.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:300'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'cook_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'rest_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'servings' => ['required', 'integer', 'min:1', 'max:999'],
            'yield' => ['nullable', 'string', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.amount' => ['nullable', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:20'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.group' => ['nullable', 'string', 'max:100'],
            'ingredients.*.known_ingredient_id' => ['nullable', 'integer', 'exists:known_ingredients,id'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.instruction' => ['required', 'string', 'max:2000'],
            'status' => ['required', 'in:draft,published'],
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recipes', 'public');
        }

        // Create recipe
        $recipe = Auth::user()->recipes()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'prep_time_minutes' => $validated['prep_time_minutes'] ?? null,
            'cook_time_minutes' => $validated['cook_time_minutes'] ?? null,
            'rest_time_minutes' => $validated['rest_time_minutes'] ?? null,
            'servings' => $validated['servings'],
            'yield' => $validated['yield'] ?? null,
            'difficulty' => $validated['difficulty'],
            'image_path' => $imagePath,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        // Save ingredients
        foreach ($validated['ingredients'] as $index => $ingredientData) {
            $knownId = $this->resolveKnownIngredient($ingredientData);
            $recipe->ingredients()->create([
                'known_ingredient_id' => $knownId,
                'amount' => $ingredientData['amount'] ?? null,
                'unit' => $ingredientData['unit'] ?? null,
                'name' => $ingredientData['name'],
                'group' => $ingredientData['group'] ?? null,
                'order' => $index,
            ]);
        }

        // Save steps
        foreach ($validated['steps'] as $index => $stepData) {
            $recipe->steps()->create([
                'instruction' => $stepData['instruction'],
                'order' => $index,
            ]);
        }

        // Attach categories
        if (!empty($validated['categories'])) {
            $recipe->categories()->attach($validated['categories']);
        }

        // Handle tags (comma-separated string)
        if (!empty($validated['tags'])) {
            $this->syncTags($recipe, $validated['tags']);
        }

        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Recipe created successfully!');
    }

    /**
     * Show the recipe edit form.
     */
    public function edit(Recipe $recipe): View
    {
        // Only the author can edit
        if (Auth::id() !== $recipe->user_id && !Auth::user()?->is_admin) {
            abort(403);
        }

        $recipe->load(['ingredients', 'steps', 'categories', 'tags']);
        $categories = Category::orderBy('name')->get();

        return view('recipes.edit', [
            'recipe' => $recipe,
            'categories' => $categories,
        ]);
    }

    /**
     * Update a recipe.
     */
    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        if (Auth::id() !== $recipe->user_id && !Auth::user()?->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:300'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'cook_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'rest_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'servings' => ['required', 'integer', 'min:1', 'max:999'],
            'yield' => ['nullable', 'string', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.amount' => ['nullable', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:20'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.group' => ['nullable', 'string', 'max:100'],
            'ingredients.*.known_ingredient_id' => ['nullable', 'integer', 'exists:known_ingredients,id'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.instruction' => ['required', 'string', 'max:2000'],
            'status' => ['required', 'in:draft,published'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($recipe->image_path) {
                Storage::disk('public')->delete($recipe->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('recipes', 'public');
        }

        // Update recipe
        $wasPublished = $recipe->status === 'published';
        $recipe->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'prep_time_minutes' => $validated['prep_time_minutes'] ?? null,
            'cook_time_minutes' => $validated['cook_time_minutes'] ?? null,
            'rest_time_minutes' => $validated['rest_time_minutes'] ?? null,
            'servings' => $validated['servings'],
            'yield' => $validated['yield'] ?? null,
            'difficulty' => $validated['difficulty'],
            'image_path' => $validated['image_path'] ?? $recipe->image_path,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' && !$wasPublished ? now() : $recipe->published_at,
        ]);

        // Replace ingredients
        $recipe->ingredients()->delete();
        foreach ($validated['ingredients'] as $index => $ingredientData) {
            $knownId = $this->resolveKnownIngredient($ingredientData);
            $recipe->ingredients()->create([
                'known_ingredient_id' => $knownId,
                'amount' => $ingredientData['amount'] ?? null,
                'unit' => $ingredientData['unit'] ?? null,
                'name' => $ingredientData['name'],
                'group' => $ingredientData['group'] ?? null,
                'order' => $index,
            ]);
        }

        // Replace steps
        $recipe->steps()->delete();
        foreach ($validated['steps'] as $index => $stepData) {
            $recipe->steps()->create([
                'instruction' => $stepData['instruction'],
                'order' => $index,
            ]);
        }

        // Sync categories
        $recipe->categories()->sync($validated['categories'] ?? []);

        // Handle tags
        if (!empty($validated['tags'])) {
            $this->syncTags($recipe, $validated['tags']);
        } else {
            $recipe->tags()->detach();
        }

        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Recipe updated successfully!');
    }

    /**
     * Delete a recipe.
     */
    public function destroy(Recipe $recipe): RedirectResponse
    {
        if (Auth::id() !== $recipe->user_id && !Auth::user()?->is_admin) {
            abort(403);
        }

        if ($recipe->image_path) {
            Storage::disk('public')->delete($recipe->image_path);
        }

        $recipe->delete();

        return redirect()->route('home')
            ->with('success', 'Recipe deleted.');
    }

    /**
     * Parse comma-separated tags and sync them.
     */
    private function syncTags(Recipe $recipe, string $tagsString): void
    {
        $tagNames = array_filter(array_map('trim', explode(',', $tagsString)));
        $tagIds = [];

        foreach ($tagNames as $name) {
            $tag = \App\Models\Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name]
            );
            $tagIds[] = $tag->id;
        }

        $recipe->tags()->sync($tagIds);
    }

    /**
     * Resolve or create a KnownIngredient for this ingredient entry.
     * If one was selected from autocomplete, use it. Otherwise, find-or-create by name.
     */
    private function resolveKnownIngredient(array $ingredientData): ?int
    {
        // If a known_ingredient_id was passed from the autocomplete, use it
        if (!empty($ingredientData['known_ingredient_id'])) {
            return (int) $ingredientData['known_ingredient_id'];
        }

        $name = trim($ingredientData['name']);
        if (empty($name)) {
            return null;
        }

        // Find or create by slug to avoid duplicates like "Olive Oil" vs "olive oil"
        $slug = \Illuminate\Support\Str::slug($name);
        if (empty($slug)) {
            return null;
        }

        $known = KnownIngredient::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'created_by' => Auth::id(),
            ]
        );

        return $known->id;
    }
}
