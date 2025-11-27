<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Store comment or reply
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->load(['user', 'likes', 'replies.user', 'replies.likes']);
        $html = view('partials.comments-item', compact('comment'))->render();

        return response()->json(['success' => true, 'comment' => $html]);
    }

    // Like/unlike comment
    public function toggleLike(Comment $comment)
    {
        $user = Auth::user();
        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            $comment->likes()->where('user_id', $user->id)->delete();
            $liked = false;
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'count' => $comment->likes()->count(),
        ]);
    }
}
