<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">No life stories. Just recipes.</h1>
            <p class="text-gray-500 mt-2">Community-driven recipes with easy metric/imperial conversion.</p>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('home', ['sort' => 'recent', 'category' => $currentCategory]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $currentSort === 'recent' ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                    Recent
                </a>
                <a href="{{ route('home', ['sort' => 'popular', 'category' => $currentCategory]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $currentSort === 'popular' ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                    Popular
                </a>
            </div>

            @if($categories->isNotEmpty())
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('home', ['sort' => $currentSort]) }}"
                       class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ !$currentCategory ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                        All
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('home', ['sort' => $currentSort, 'category' => $category->slug]) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ $currentCategory === $category->slug ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recipe Grid -->
        @if($recipes->isEmpty())
            <div class="text-center py-20">
                <p class="text-gray-500 text-lg">No recipes yet. Be the first to add one!</p>
                @auth
                    <a href="{{ route('recipes.create') }}" class="inline-block mt-4 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition-colors">
                        Add a Recipe
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block mt-4 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition-colors">
                        Sign up to add recipes
                    </a>
                @endauth
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recipes as $recipe)
                    <x-recipe-card :recipe="$recipe" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $recipes->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
