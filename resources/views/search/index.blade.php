<x-app-layout>
    <x-slot name="title">Search: {{ $query }}</x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            @if($query)
                Search results for "{{ $query }}"
            @else
                Search Recipes
            @endif
        </h1>

        <form action="{{ route('search') }}" method="GET" class="mb-8">
            <div class="flex gap-2">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search recipes..."
                    class="flex-1 rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" autofocus>
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                    Search
                </button>
            </div>
        </form>

        @if($query && $recipes instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($recipes->isEmpty())
                <p class="text-gray-500">No recipes found for "{{ $query }}". Try different keywords.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recipes as $recipe)
                        <x-recipe-card :recipe="$recipe" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $recipes->links() }}</div>
            @endif
        @elseif($query)
            <p class="text-gray-500">Please enter at least 2 characters to search.</p>
        @endif
    </div>
</x-app-layout>
