<x-app-layout>
    <x-slot name="title">Add Recipe</x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Add a Recipe</h1>

        <form method="POST" action="{{ route('recipes.store') }}" enctype="multipart/form-data"
              x-data="recipeForm()" class="space-y-8">
            @csrf

            <!-- Title -->
            <div>
                <x-input-label for="title" value="Title" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                    :value="old('title')" required placeholder="e.g. Classic Margherita Pizza" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <!-- Description -->
            <div>
                <x-input-label for="description" value="Short Description (optional, max 300 chars)" />
                <textarea id="description" name="description" rows="2" maxlength="300"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    placeholder="Keep it brief. Just the essentials.">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Time & Servings -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <x-input-label for="prep_time_minutes" value="Prep (min)" />
                    <x-text-input id="prep_time_minutes" name="prep_time_minutes" type="number" min="0" class="mt-1 block w-full" :value="old('prep_time_minutes')" />
                </div>
                <div>
                    <x-input-label for="cook_time_minutes" value="Cook (min)" />
                    <x-text-input id="cook_time_minutes" name="cook_time_minutes" type="number" min="0" class="mt-1 block w-full" :value="old('cook_time_minutes')" />
                </div>
                <div>
                    <x-input-label for="servings" value="Servings" />
                    <x-text-input id="servings" name="servings" type="number" min="1" class="mt-1 block w-full" :value="old('servings', 4)" required />
                </div>
                <div>
                    <x-input-label for="difficulty" value="Difficulty" />
                    <select id="difficulty" name="difficulty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ old('difficulty', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rest_time_minutes" value="Rest/Rise Time (min, optional)" />
                    <x-text-input id="rest_time_minutes" name="rest_time_minutes" type="number" min="0" class="mt-1 block w-full" :value="old('rest_time_minutes')" />
                </div>
                <div>
                    <x-input-label for="yield" value="Yield (optional, e.g. '12 cookies')" />
                    <x-text-input id="yield" name="yield" type="text" class="mt-1 block w-full" :value="old('yield')" />
                </div>
            </div>

            <!-- Image -->
            <div>
                <x-input-label for="image" value="Photo (optional, max 2MB, jpeg/png/webp)" />
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <!-- Categories -->
            @if($categories->isNotEmpty())
                <div>
                    <x-input-label value="Categories" />
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($categories as $category)
                            <label class="inline-flex items-center gap-1.5">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                                    {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tags -->
            <div>
                <x-input-label for="tags" value="Tags (comma-separated, optional)" />
                <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full"
                    :value="old('tags')" placeholder="e.g. vegetarian, quick, summer" />
            </div>

            <!-- Ingredients (dynamic) -->
            <div>
                <x-input-label value="Ingredients" />
                <x-input-error :messages="$errors->get('ingredients')" class="mt-2" />

                <div class="space-y-3 mt-2">
                    <template x-for="(ingredient, index) in ingredients" :key="index">
                        <div class="flex gap-2 items-start">
                            <input type="number" x-bind:name="'ingredients[' + index + '][amount]'"
                                x-model="ingredient.amount" step="0.01" min="0" placeholder="Amt"
                                class="w-20 rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <select x-bind:name="'ingredients[' + index + '][unit]'" x-model="ingredient.unit"
                                class="w-24 rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">—</option>
                                <optgroup label="Metric">
                                    <option value="g">g</option>
                                    <option value="kg">kg</option>
                                    <option value="ml">ml</option>
                                    <option value="l">l</option>
                                </optgroup>
                                <optgroup label="Imperial">
                                    <option value="cup">cup</option>
                                    <option value="tbsp">tbsp</option>
                                    <option value="tsp">tsp</option>
                                    <option value="oz">oz</option>
                                    <option value="lb">lb</option>
                                    <option value="fl oz">fl oz</option>
                                </optgroup>
                                <optgroup label="Other">
                                    <option value="pinch">pinch</option>
                                    <option value="piece">piece</option>
                                    <option value="slice">slice</option>
                                    <option value="clove">clove</option>
                                    <option value="bunch">bunch</option>
                                    <option value="can">can</option>
                                    <option value="whole">whole</option>
                                </optgroup>
                            </select>
                            <div class="flex-1 relative" x-data="ingredientAutocomplete(ingredient)">
                                <input type="text" x-bind:name="'ingredients[' + index + '][name]'"
                                    x-model="query" required placeholder="Ingredient name"
                                    @focus="query.length >= 2 && (showSuggestions = suggestions.length > 0)"
                                    @blur="close()"
                                    autocomplete="off"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                                <input type="hidden" x-bind:name="'ingredients[' + index + '][known_ingredient_id]'" x-model="selectedId">
                                <div x-show="showSuggestions" x-cloak
                                     class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                    <template x-for="s in suggestions" :key="s.id">
                                        <button type="button" @click="select(s)"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-orange-50 flex justify-between items-center">
                                            <span x-text="s.name"></span>
                                            <span x-show="s.brand" x-text="s.brand" class="text-xs text-gray-400 ml-2"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <input type="text" x-bind:name="'ingredients[' + index + '][group]'"
                                x-model="ingredient.group" placeholder="Group"
                                class="w-28 rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500 hidden sm:block">
                            <button type="button" @click="removeIngredient(index)" x-show="ingredients.length > 1"
                                class="p-2 text-red-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addIngredient()"
                    class="mt-3 text-sm text-orange-600 hover:text-orange-700 font-medium">
                    + Add Ingredient
                </button>
            </div>

            <!-- Steps (dynamic) -->
            <div>
                <x-input-label value="Instructions" />
                <x-input-error :messages="$errors->get('steps')" class="mt-2" />

                <div class="space-y-3 mt-2">
                    <template x-for="(step, index) in steps" :key="index">
                        <div class="flex gap-3 items-start">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm mt-1"
                                 x-text="index + 1"></div>
                            <textarea x-bind:name="'steps[' + index + '][instruction]'"
                                x-model="step.instruction" required rows="2" placeholder="Describe this step..."
                                class="flex-1 rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                            <button type="button" @click="removeStep(index)" x-show="steps.length > 1"
                                class="p-2 text-red-400 hover:text-red-600 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addStep()"
                    class="mt-3 text-sm text-orange-600 hover:text-orange-700 font-medium">
                    + Add Step
                </button>
            </div>

            <!-- Submit -->
            <div class="flex gap-4 pt-4 border-t border-gray-200">
                <button type="submit" name="status" value="published"
                    class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                    Publish Recipe
                </button>
                <button type="submit" name="status" value="draft"
                    class="bg-white text-gray-600 px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors font-medium">
                    Save as Draft
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
