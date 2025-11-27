<li class="border rounded-lg p-3" id="comment-{{ $comment->id }}">
    <div class="flex items-start gap-3">
        <x-user-avatar :user="$comment->user" class="w-8 h-8"/>
        <div class="flex-1">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-gray-800">{{ $comment->user->name }}</span>
                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="mt-1 text-gray-700">{{ $comment->content }}</p>

            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                @auth
                <button onclick="submitReplyForm({{ $comment->id }})">Reply</button>
                <button onclick="toggleLike({{ $comment->id }})">
                    👍 <span id="comment-like-count-{{ $comment->id }}">{{ $comment->likes()->count() }}</span>
                </button>
                @endauth
            </div>

            <!-- Reply Form (hidden by default) -->
            @auth
            <div x-data="{ showReply: false }" x-show="showReply" class="mt-2">
                <form onsubmit="submitReply(event, {{ $comment->id }})">
                    @csrf
                    <textarea name="content" rows="2" class="w-full border rounded-md p-2 mb-1" placeholder="Write a reply..." required></textarea>
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700">Reply</button>
                </form>
            </div>
            @endauth

            <!-- Nested Replies -->
            @if($comment->replies->count() > 0)
                <ul class="mt-2 ml-6 space-y-2">
                    @foreach($comment->replies as $reply)
                        @include('partials.comment', ['comment' => $reply])
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</li>

<script>
    function submitReplyForm(commentId){
        const replyContainer = document.querySelector(`#comment-${commentId} [x-data]`);
        if(replyContainer) replyContainer.__x.$data.showReply = true;
        const textarea = replyContainer.querySelector('textarea');
        if(textarea) textarea.focus();
    }
</script>
