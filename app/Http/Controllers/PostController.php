<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostCreateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $categories = Category::all();
        // FIXED: Added published posts filter
        $posts = Post::with('user', 'category')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->latest()
                    ->paginate(5);
        
        return view('post.index', compact('categories', 'posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('post.form', compact('categories'));
    }

    public function store(PostCreateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        // FIXED: Set published_at if not set
        if (!isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = Post::create($data);

        if ($request->hasFile('image')) {
            $post->addMedia($request->file('image'))->toMediaCollection('image');
        }

        return redirect()->route('dashboard')->with('success', 'Post created successfully!');
    }

    public function show($username, Post $post)
    {
        // FIXED: Convert published_at to Carbon instance before comparison
        $publishedAt = $post->published_at ? Carbon::parse($post->published_at) : null;
        
        if (!$publishedAt || $publishedAt->gt(now())) {
            if (Auth::id() !== $post->user_id) {
                abort(404);
            }
        }

        $post->load(['user', 'category']);

        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->with(['user', 'likes', 'replies.user', 'replies.likes', 'replies.replies.user'])
            ->latest()
            ->get();

        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->limit(6)
            ->get();

        return view('post.show', compact('post', 'comments', 'relatedPosts'));
    }

    public function category(Category $category)
    {
        // FIXED: Added published posts filter
        $posts = $category->posts()
                    ->with('user')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->latest()
                    ->paginate(5);
        $categories = Category::all();

        return view('post.index', compact('posts', 'categories', 'category'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::all();
        return view('post.form', compact('post', 'categories'));
    }

    public function myPosts()
    {
        $user = auth()->user();
        // FIXED: Show all user posts (including drafts) in my-posts
        $posts = $user->posts()
            ->with(['category', 'claps', 'user'])
            ->latest()
            ->paginate(6);

        return view('post.my-posts', compact('posts', 'user'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
        ]);

        $updateData = [
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
        ];

        // Update slug only if title changed
        if ($post->title !== $request->title) {
            $updateData['slug'] = Str::slug($request->title) . '-' . uniqid();
        }

        // Update published_at if provided
        if ($request->has('published_at')) {
            $updateData['published_at'] = $request->published_at;
        }

        $post->update($updateData);

        if ($request->hasFile('image')) {
            $post->clearMediaCollection('image');
            $post->addMedia($request->file('image'))->toMediaCollection('image');
        }

        return redirect()->route('dashboard')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->clearMediaCollection('image');
        $post->delete();

        return redirect()->route('dashboard')->with('success', 'Post deleted successfully.');
    }

    // =============================
    // 🔥 AJAX Clap (Like) Toggle
    // =============================
    public function toggleClap(Post $post)
    {
        $user = auth()->user();

        $existingClap = $post->claps()->where('user_id', $user->id)->first();
        if ($existingClap) {
            $existingClap->delete();
            $clapped = false;
        } else {
            $post->claps()->create(['user_id' => $user->id]);
            $clapped = true;
        }

        return response()->json([
            'status' => 'success',
            'clapped' => $clapped,
            'count' => $post->claps()->count(),
        ]);
    }
}