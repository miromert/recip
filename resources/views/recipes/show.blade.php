<x-app-layout>
    @php
        // Build JSON-LD structured data
        $jsonLdData = [
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => $recipe->title,
            'description' => $recipe->description ?? '',
            'author' => ['@type' => 'Person', 'name' => $recipe->user->name],
            'datePublished' => $recipe->published_at?->toIso8601String(),
            'prepTime' => $recipe->prep_time_minutes ? 'PT' . $recipe->prep_time_minutes . 'M' : null,
            'cookTime' => $recipe->cook_time_minutes ? 'PT' . $recipe->cook_time_minutes . 'M' : null,
            'totalTime' => $recipe->total_time_minutes ? 'PT' . $recipe->total_time_minutes . 'M' : null,
            'recipeYield' => $recipe->yield ?? $recipe->servings . ' servings',
            'recipeCategory' => $recipe->categories->pluck('name')->first(),
            'recipeIngredient' => $recipe->ingredients->map(fn($i) => $i->formatted)->toArray(),
            'recipeInstructions' => $recipe->steps->map(fn($s) => ['@type' => 'HowToStep', 'text' => $s->instruction])->toArray(),
        ];
        if ($recipe->image_path) {
            $jsonLdData['image'] = [asset('storage/' . $recipe->image_path)];
        }
        if ($recipe->votes_count > 0) {
            $jsonLdData['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => '5',
                'ratingCount' => (string) $recipe->votes_count,
            ];
        }
    @endphp

    <x-slot name="title">{{ $recipe->title }}</x-slot>
    <x-slot name="metaDescription">{{ $recipe->description ?? 'A recipe on Recip' }}</x-slot>
    <x-slot name="ogType">article</x-slot>
    @if($recipe->image_path)
        <x-slot name="ogImage">{{ asset('storage/' . $recipe->image_path) }}</x-slot>
    @endif
    <x-slot name="jsonLd">{!! json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</x-slot>

    <article class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="unitConverter()">
        <!-- E-book style wrapper -->
        <div class="bg-[#faf7f2] border border-[#e8e0d4] rounded-2xl shadow-sm px-6 sm:px-10 lg:px-14 py-10">

        <!-- Title & Meta -->
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-[#2c2c2c] leading-tight">{{ $recipe->title }}</h1>

            @if($recipe->description)
                <p class="text-[#555] mt-3 text-lg leading-relaxed">{{ $recipe->description }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-[#888]">
                <a href="{{ route('users.show', $recipe->user) }}" class="hover:text-[#555]">
                    by <span class="font-medium text-[#444]">{{ $recipe->user->name }}</span>
                </a>
                @if($recipe->published_at)
                    <time datetime="{{ $recipe->published_at->toIso8601String() }}">
                        {{ $recipe->published_at->format('M j, Y') }}
                    </time>
                @endif
                @foreach($recipe->categories as $category)
                    <span class="bg-[#ece6dc] text-[#555] px-2 py-0.5 rounded-full text-xs">{{ $category->name }}</span>
                @endforeach
            </div>
        </header>

        <!-- Hero Image -->
        @if($recipe->image_path)
            <div class="mb-8">
                <img src="{{ asset('storage/' . $recipe->image_path) }}"
                     alt="{{ $recipe->title }}"
                     class="w-full max-h-96 object-cover rounded-lg">
            </div>
        @endif

        <!-- Meta Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8 p-4 bg-[#f3efe8] rounded-lg border border-[#e0d8cc]">
            <div class="text-center">
                <div class="text-xs text-[#999] uppercase tracking-wide">Prep</div>
                <div class="font-semibold text-[#2c2c2c]">{{ $recipe->formatted_prep_time }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-[#999] uppercase tracking-wide">Cook</div>
                <div class="font-semibold text-[#2c2c2c]">{{ $recipe->formatted_cook_time }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-[#999] uppercase tracking-wide">Total</div>
                <div class="font-semibold text-[#2c2c2c]">{{ $recipe->formatted_total_time }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-[#999] uppercase tracking-wide">Servings</div>
                <div class="font-semibold text-[#2c2c2c]">{{ $recipe->servings }}{{ $recipe->yield ? ' (' . $recipe->yield . ')' : '' }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-[#999] uppercase tracking-wide">Difficulty</div>
                <div class="font-semibold text-[#2c2c2c] capitalize">{{ $recipe->difficulty }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Ingredients (sidebar) -->
            <aside class="lg:col-span-1">
                <div class="bg-[#f3efe8] rounded-lg border border-[#e0d8cc] p-6 sticky top-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-[#2c2c2c]">Ingredients</h2>
                        <!-- Unit Toggle -->
                        <button @click="toggleSystem()"
                            class="text-xs font-medium px-3 py-1.5 rounded-full border transition-colors"
                            :class="system === 'metric' ? 'bg-[#ece6dc] border-[#d5cec2] text-[#444]' : 'bg-[#e8e2d8] border-[#c8c0b4] text-[#444]'">
                            <span x-text="system === 'metric' ? 'Metric' : 'Imperial'"></span>
                        </button>
                    </div>

                    @php
                        $grouped = $recipe->ingredients->groupBy('group');
                    @endphp

                    @foreach($grouped as $group => $ingredients)
                        @if($group)
                            <h3 class="font-semibold text-[#555] text-sm mt-4 mb-2">{{ $group }}</h3>
                        @endif
                        <ul class="space-y-2">
                            @foreach($ingredients as $ingredient)
                                <li class="flex items-start gap-2" x-data="{ checked: false }">
                                    <input type="checkbox" x-model="checked"
                                        class="mt-1 rounded border-[#ccc] text-[#555] focus:ring-[#bbb]">
                                    <span :class="checked ? 'line-through text-[#bbb]' : 'text-[#444]'"
                                          class="text-sm transition-colors">
                                        <span x-text="convertIngredient({{ json_encode($ingredient->amount) }}, '{{ $ingredient->unit }}', '{{ e($ingredient->name) }}')">
                                            {{ $ingredient->formatted }}
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </aside>

            <!-- Steps (main content) -->
            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-[#2c2c2c] mb-6">Instructions</h2>
                <ol class="space-y-6">
                    @foreach($recipe->steps as $step)
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#2c2c2c] text-[#faf7f2] flex items-center justify-center font-bold text-sm">
                                {{ $step->order + 1 }}
                            </div>
                            <div class="text-[#444] leading-relaxed pt-1">
                                {{ $step->instruction }}
                            </div>
                        </li>
                    @endforeach
                </ol>

                <!-- Tags -->
                @if($recipe->tags->isNotEmpty())
                    <div class="mt-8 flex flex-wrap gap-2">
                        @foreach($recipe->tags as $tag)
                            <span class="bg-[#ece6dc] text-[#666] px-2 py-1 rounded text-xs">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif

                <!-- Actions Bar -->
                <div class="mt-8 pt-6 border-t border-[#e0d8cc] flex flex-wrap items-center gap-4">
                    <!-- Vote Button -->
                    <div x-data="voteButton({{ $recipe->id }}, {{ $hasVoted ? 'true' : 'false' }}, {{ $recipe->votes_count }})">
                        <button @click="toggle()" @if(!auth()->check()) disabled @endif
                            class="flex items-center gap-2 px-4 py-2 rounded-lg border transition-colors disabled:opacity-50"
                            :class="voted ? 'bg-[#e8e2d8] border-[#bbb] text-[#333]' : 'bg-[#f3efe8] border-[#d5cec2] text-[#666] hover:bg-[#ece6dc]'">>
                            <svg class="w-5 h-5" :class="voted ? 'fill-[#555]' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span x-text="count"></span>
                        </button>
                        @guest
                            <p class="text-xs text-gray-400 mt-1"><a href="{{ route('login') }}" class="underline">Log in</a> to vote</p>
                        @endguest
                    </div>

                    <!-- Basket Button -->
                    <button @click="$store.basket.toggle({ id: {{ $recipe->id }}, slug: '{{ $recipe->slug }}', title: '{{ e($recipe->title) }}' })"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg border transition-colors"
                        :class="$store.basket.has({{ $recipe->id }}) ? 'bg-orange-100 border-orange-300 text-orange-700' : 'border-[#d5cec2] text-[#666] hover:bg-[#ece6dc]'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        <span x-text="$store.basket.has({{ $recipe->id }}) ? 'In Basket ✓' : 'Add to Basket'"></span>
                    </button>

                    <!-- Print Button -->
                    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 rounded-lg border border-[#d5cec2] text-[#666] hover:bg-[#ece6dc] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </button>

                    <!-- Edit / Delete (author only) -->
                    @auth
                        @if(Auth::id() === $recipe->user_id || Auth::user()->is_admin)
                            <a href="{{ route('recipes.edit', $recipe) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg border border-[#d5cec2] text-[#666] hover:bg-[#ece6dc] transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('recipes.destroy', $recipe) }}" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition-colors">
                                    Delete
                                </button>
                            </form>
                        @endif
                    @endauth

                    <!-- Report -->
                    @auth
                        @if(Auth::id() !== $recipe->user_id)
                            <div x-data="{ reportOpen: false }" class="relative">
                                <button @click="reportOpen = !reportOpen" class="text-xs text-[#999] hover:text-[#666] underline">
                                    Report
                                </button>
                                <div x-show="reportOpen" @click.away="reportOpen = false" x-cloak
                                     class="absolute bottom-full mb-2 right-0 w-72 bg-white border border-[#e0d8cc] rounded-lg shadow-lg p-4 z-10">
                                    <form method="POST" action="{{ route('recipes.report', $recipe) }}">
                                        @csrf
                                        <label class="block text-sm font-medium text-[#444] mb-2">Why are you reporting this?</label>
                                        <textarea name="reason" required rows="3" maxlength="1000"
                                            class="w-full rounded-lg border-[#ccc] text-sm focus:border-[#999] focus:ring-[#bbb]"></textarea>
                                        <button type="submit" class="mt-2 w-full bg-red-600 text-white text-sm py-2 rounded-lg hover:bg-red-700">
                                            Submit Report
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        </div><!-- end e-book wrapper -->
    </article>
</x-app-layout>
