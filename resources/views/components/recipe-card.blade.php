@props(['recipe'])

<article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
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
            @if($recipe->categories->isNotEmpty())
                <span class="text-xs text-orange-600">{{ $recipe->categories->first()->name }}</span>
            @endif
        </div>
    </div>
</article>
