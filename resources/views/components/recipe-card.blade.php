@props(['recipe'])

<article x-data class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
    <a href="{{ route('recipes.show', $recipe) }}">
        @if($recipe->image_path)
            <img src="{{ asset('storage/' . $recipe->image_path) }}"
                 alt="{{ $recipe->title }}"
                 class="w-full h-48 object-cover"
                 loading="lazy">
        @else
            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        @endif
    </a>

    <div class="p-4">
        <a href="{{ route('recipes.show', $recipe) }}" class="block">
            <h3 class="font-semibold text-gray-900 text-lg leading-tight hover:text-orange-600 transition-colors">
                {{ $recipe->title }}
            </h3>
        </a>

        @if($recipe->description)
            <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ $recipe->description }}</p>
        @endif

        <div class="flex items-center gap-3 mt-3 text-xs text-gray-500">
            @if($recipe->total_time_minutes)
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $recipe->formatted_total_time }}
                </span>
            @endif
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                {{ $recipe->votes_count ?? 0 }}
            </span>
            <span class="capitalize">{{ $recipe->difficulty }}</span>
        </div>

        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
            <a href="{{ route('users.show', $recipe->user) }}" class="text-xs text-gray-500 hover:text-gray-700">
                {{ $recipe->user->name }}
            </a>
            <div class="flex items-center gap-2">
                @if($recipe->categories->isNotEmpty())
                    <span class="text-xs text-orange-600">{{ $recipe->categories->first()->name }}</span>
                @endif
                <button @click.prevent="$store.basket.toggle({ id: {{ $recipe->id }}, slug: '{{ $recipe->slug }}', title: '{{ e($recipe->title) }}' })"
                    class="p-1 rounded transition-colors"
                    :class="$store.basket.has({{ $recipe->id }}) ? 'text-orange-600' : 'text-gray-400 hover:text-gray-600'"
                    :title="$store.basket.has({{ $recipe->id }}) ? 'Remove from basket' : 'Add to basket'">
                    <svg class="w-4 h-4" :fill="$store.basket.has({{ $recipe->id }}) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </button>
            </div>
        </div>
    </div>
</article>
