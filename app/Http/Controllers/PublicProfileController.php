<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;   // <-- FIXED: Correct import with proper case

class PublicProfileController extends Controller
{
    public function show(User $user)
    {
        // Load posts of this user (make sure User model has posts() relationship)
        $posts = $user->posts()->latest()->paginate(10);

        return view('profile.show', [
            'user'  => $user,
            'posts' => $posts,
        ]);
    }
}
