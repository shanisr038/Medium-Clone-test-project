<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ClapController extends Controller
{
    public function toggle(Post $post)
    {
        $user = Auth::user();

        if ($post->isClappedBy($user)) {
            $post->claps()->where('user_id', $user->id)->delete();
            $clapped = false;
        } else {
            $post->claps()->create(['user_id' => $user->id]);
            $clapped = true;
        }

        return response()->json([
            'success' => true,
            'clapped' => $clapped,
            'count' => $post->claps()->count()
        ]);
    }
}
