@props(['comment'])

<div class="border p-3 rounded bg-gray-50">
    <div class="flex justify-between items-center">
        <div>
            <strong>{{ $comment->user->name }}</strong> 
            <span class="text-gray-500 text-sm">{{ $comment->created_at->diffForHumans() }}</span>
        </div>

        <div class="flex items-center gap-2">
            @auth
            <button 
                @click.prevent="toggleLike({{ $comment->id }}, $event)"
                class="text-gray-500 hover:text-blue-600">
                👍 <span id="comment-like-count-{{ $comment->id }}">{{ $comment->likesCount() }}</span>
            </button>
            @endauth
            <button class="text-gray-500 hover:text-blue-600" @click="$refs['reply-{{ $comment->id }}'].classList.toggle('hidden')">Reply</button>
        </div>
    </div>

    <p class="mt-2">{{ $comment->content }}</p>

    {{-- Reply form --}}
    @auth
    <form action="{{ route('comments.store', $comment->post) }}" method="POST" class="mt-2 hidden" x-ref="reply-{{ $comment->id }}">
        @csrf
        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
        <textarea name="content" required class="w-full border rounded p-2" placeholder="Reply..."></textarea>
        <button type="submit" class="mt-1 bg-green-600 text-white px-3 py-1 rounded text-sm">Reply</button>
    </form>
    @endauth

    {{-- Nested replies --}}
    @if($comment->replies->count())
        <div class="ml-6 mt-2 space-y-2">
            @foreach ($comment->replies as $reply)
                <x-comment-item :comment="$reply" />
            @endforeach
        </div>
    @endif
</div>
