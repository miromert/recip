<x-app-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center">
                    @if($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover">
                    @else
                        <span class="text-2xl font-bold text-orange-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500">{{'@'}}{{ $user->username }} · Joined {{ $user->created_at->format('M Y') }}</p>
                    @if($user->bio)
                        <p class="text-gray-600 mt-1">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-6 mt-4 text-sm text-gray-500">
                <span><span class="font-semibold text-gray-900">{{ $recipes->total() }}</span> recipes</span>
                <span><span class="font-semibold text-gray-900">{{ $user->total_votes_received }}</span> total votes</span>
            </div>
        </div>

        <!-- Recipes -->
        @if($recipes->isEmpty())
            <p class="text-gray-500 text-center py-12">No published recipes yet.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recipes as $recipe)
                    <x-recipe-card :recipe="$recipe" />
                @endforeach
            </div>
            <div class="mt-8">{{ $recipes->links() }}</div>
        @endif
    </div>
</x-app-layout>
