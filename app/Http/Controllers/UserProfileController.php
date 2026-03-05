<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Show a user's public profile.
     */
    public function show(User $user): View
    {
        $recipes = $user->recipes()
            ->published()
            ->withCount('votes')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('users.show', [
            'user' => $user,
            'recipes' => $recipes,
        ]);
    }
}
