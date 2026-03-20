<x-app-layout>
    <x-slot name="title">Shopping Basket</x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="basketPage()">
        <div class="bg-[#faf7f2] border border-[#e8e0d4] rounded-2xl shadow-sm px-6 sm:px-10 lg:px-14 py-10">

            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-[#2c2c2c]">Shopping Basket</h1>
                <div class="flex items-center gap-3" x-show="recipes.length > 0" x-cloak>
                    <!-- Unit Toggle -->
                    <button @click="toggleSystem()"
                        class="text-xs font-medium px-3 py-1.5 rounded-full border transition-colors"
                        :class="system === 'metric' ? 'bg-[#ece6dc] border-[#d5cec2] text-[#444]' : 'bg-[#e8e2d8] border-[#c8c0b4] text-[#444]'">
                        <span x-text="system === 'metric' ? 'Metric' : 'Imperial'"></span>
                    </button>
                    <!-- Print -->
                    <button onclick="window.print()" class="text-xs font-medium px-3 py-1.5 rounded-full border border-[#d5cec2] text-[#666] hover:bg-[#ece6dc] transition-colors">
                        Print
                    </button>
                    <!-- Clear -->
                    <button @click="clearAll()" class="text-xs font-medium px-3 py-1.5 rounded-full border border-red-300 text-red-600 hover:bg-red-50 transition-colors">
                        Clear all
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div x-show="loading" class="text-center py-12 text-[#888]">
                Loading your shopping list...
            </div>

            <!-- Error -->
            <div x-show="error && !loading" x-cloak class="text-center py-12">
                <p class="text-red-600 mb-2">Something went wrong loading your basket.</p>
                <button @click="load()" class="text-sm text-orange-600 underline">Try again</button>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && !error && $store.basket.count === 0" class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-[#ccc] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <p class="text-[#888] text-lg mb-2">Your basket is empty</p>
                <p class="text-[#aaa] text-sm mb-4">Add recipes to your basket to build a shopping list.</p>
                <a href="{{ route('home') }}" class="inline-block bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                    Browse recipes
                </a>
            </div>

            <!-- Basket Content -->
            <div x-show="!loading && !error && recipes.length > 0" x-cloak>

                <!-- Recipes in Basket -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-[#2c2c2c] mb-3">Recipes</h2>
                    <ul class="space-y-2">
                        <template x-for="recipe in recipes" :key="recipe.id">
                            <li class="flex items-center justify-between bg-[#f3efe8] rounded-lg px-4 py-3 border border-[#e0d8cc]">
                                <a :href="'/recipes/' + recipe.slug" class="text-[#444] hover:text-orange-600 font-medium transition-colors" x-text="recipe.title"></a>
                                <button @click="removeRecipe(recipe.id)" class="text-[#999] hover:text-red-600 transition-colors" title="Remove from basket">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Aggregated Shopping List -->
                <div>
                    <h2 class="text-lg font-semibold text-[#2c2c2c] mb-3">Shopping List</h2>
                    <ul class="space-y-2">
                        <template x-for="(item, index) in ingredients" :key="index">
                            <li class="flex items-start gap-2" x-data="{ checked: false }">
                                <input type="checkbox" x-model="checked"
                                    class="mt-1 rounded border-[#ccc] text-[#555] focus:ring-[#bbb]">
                                <span :class="checked ? 'line-through text-[#bbb]' : 'text-[#444]'"
                                      class="text-sm transition-colors"
                                      x-text="convertIngredient(item.amount, item.unit, item.name)">
                                </span>
                            </li>
                        </template>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
