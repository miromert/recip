<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Admin Dashboard</h1>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</div>
                <div class="text-sm text-gray-500">Users</div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $totalRecipes }}</div>
                <div class="text-sm text-gray-500">Recipes</div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $totalPublished }}</div>
                <div class="text-sm text-gray-500">Published</div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $flaggedRecipes->count() }}</div>
                <div class="text-sm text-gray-500">Flagged</div>
            </div>
        </div>

        <!-- Flagged Recipes -->
        @if($flaggedRecipes->isNotEmpty())
            <h2 class="text-lg font-bold text-gray-900 mb-4">Flagged Recipes</h2>
            <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-200 mb-8">
                @foreach($flaggedRecipes as $recipe)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <a href="{{ route('recipes.show', $recipe) }}" class="font-medium text-gray-900 hover:text-orange-600">{{ $recipe->title }}</a>
                            <span class="text-sm text-gray-500 ml-2">{{ $recipe->reports_count }} reports</span>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.recipes.restore', $recipe) }}">
                                @csrf
                                <button class="text-sm bg-green-50 text-green-700 px-3 py-1 rounded hover:bg-green-100">Restore</button>
                            </form>
                            <form method="POST" action="{{ route('admin.recipes.archive', $recipe) }}">
                                @csrf
                                <button class="text-sm bg-red-50 text-red-700 px-3 py-1 rounded hover:bg-red-100">Archive</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pending Reports -->
        <h2 class="text-lg font-bold text-gray-900 mb-4">Pending Reports</h2>
        @if($pendingReports->isEmpty())
            <p class="text-gray-500">No pending reports.</p>
        @else
            <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-200">
                @foreach($pendingReports as $report)
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('recipes.show', $report->recipe) }}" class="font-medium text-gray-900 hover:text-orange-600">{{ $report->recipe->title }}</a>
                                <p class="text-sm text-gray-500 mt-1">Reported by {{ $report->user->name }} · {{ $report->created_at->diffForHumans() }}</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $report->reason }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                                @csrf
                                <button class="text-sm bg-gray-50 text-gray-600 px-3 py-1 rounded hover:bg-gray-100">Dismiss</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
