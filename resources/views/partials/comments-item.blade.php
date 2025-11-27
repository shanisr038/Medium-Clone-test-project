<li id="comment-{{ $comment->id }}" x-data="{ showReply: false }" class="border rounded-md p-3 space-y-2">
    <div class="flex justify-between items-start">
        <div class="flex items-center gap-2">
            <x-user-avatar :user="$comment->user" class="w-8 h-8" />
            <div>
                <span class="font-semibold">{{ $comment->user->name }}</span>
                <p class="text-gray-600 text-sm">{{ $comment->content }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 text-sm text-gray-500">
            <button onclick="toggleLike({{ $comment->id }})">
                👍 <span id="comment-like-count-{{ $comment->id }}">{{ $comment->likes->count() }}</span>
            </button>
            <button @click="showReply = !showReply">Reply</button>
        </div>
    </div>

    <!-- Reply Form -->
    <div x-show="showReply" class="mt-2">
        <form onsubmit="submitReply(event, {{ $comment->id }})" class="flex gap-2">
            @csrf
            <input type="text" name="content" placeholder="Write a reply..." required class="flex-1 border rounded-md p-2 text-sm"/>
            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm">Reply</button>
        </form>
    </div>

    <!-- Replies -->
    @if($comment->replies->count() > 0)
    <ul class="ml-6 mt-2 space-y-2">
        @foreach($comment->replies as $reply)
            @include('partials.comments-item', ['comment' => $reply])
        @endforeach
    </ul>
    @endif
</li>
