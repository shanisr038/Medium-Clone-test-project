@props(['comment'])
<li class="border rounded-md p-3" x-data="{ showReply: false }">
    <div class="flex justify-between items-center mb-1">
        <span class="font-semibold">{{ $comment->user->name }}</span>
        <span class="text-gray-500 text-sm">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <p class="text-gray-700 mb-2">{{ $comment->content }}</p>

    <div class="flex gap-4 text-sm mb-2">
        <button onclick="toggleLike({{ $comment->id }})" class="text-blue-600 hover:underline">
            👍 Like (<span id="comment-like-count-{{ $comment->id }}">{{ $comment->likes()->count() }}</span>)
        </button>

        @auth
        <button type="button" @click="showReply = !showReply" class="text-blue-600 hover:underline">
            💬 Reply
        </button>
        @endauth
    </div>

    @auth
    <div x-show="showReply" x-transition class="mb-2 ml-4">
        <form onsubmit="submitReply(event, {{ $comment->id }})">
            @csrf
            <textarea name="content" rows="2" class="w-full border rounded-md p-2 mb-2" placeholder="Write a reply..." required></textarea>
            <button type="submit" class="bg-gray-200 px-3 py-1 rounded-md hover:bg-gray-300 text-sm">Reply</button>
        </form>
    </div>
    @endauth

    @if($comment->replies->count() > 0)
        <ul class="mt-2 ml-6 space-y-2">
            @foreach($comment->replies as $reply)
                @include('partials.comment', ['comment' => $reply])
            @endforeach
        </ul>
    @endif
</li>
