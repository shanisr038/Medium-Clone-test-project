<div class="space-y-6">

    <!-- Comment Form -->
    @auth
        <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-6">
            @csrf
            <textarea name="content" rows="3" class="w-full border rounded-md p-3 mb-2 focus:ring focus:ring-blue-200" placeholder="Add a comment..."></textarea>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                Comment
            </button>
        </form>
    @else
        <p class="text-gray-500">Please <a href="{{ route('login') }}" class="underline text-blue-600">login</a> to comment.</p>
    @endauth

    <!-- Comments List -->
    <ul class="space-y-4">
        @foreach ($comments as $comment)
            <li class="border rounded-lg p-4 bg-gray-50">
                <div class="flex items-start gap-3">
                    <x-user-avatar :user="$comment->user" class="w-10 h-10" />
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold">{{ $comment->user->name }}</span>
                            <span class="text-gray-400 text-sm">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-gray-700">{{ $comment->content }}</p>

                        <!-- Comment actions: Like & Reply -->
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                            <button type="button" onclick="toggleLike({{ $comment->id }})" class="hover:text-blue-600 flex items-center gap-1">
                                👍 <span id="comment-like-count-{{ $comment->id }}">{{ $comment->likes_count ?? 0 }}</span>
                            </button>

                            @auth
                                <button type="button" @click="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')" class="hover:text-blue-600">
                                    Reply
                                </button>
                            @endauth
                        </div>

                        <!-- Reply form (hidden by default) -->
                        @auth
                            <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store', $post->id) }}" method="POST" class="mt-2 ml-6 hidden">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <textarea name="content" rows="2" class="w-full border rounded-md p-2 mb-1 focus:ring focus:ring-blue-200" placeholder="Reply..."></textarea>
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm transition">
                                    Reply
                                </button>
                            </form>
                        @endauth

                        <!-- Nested replies -->
                        @if ($comment->replies->count() > 0)
                            <ul class="mt-4 ml-6 space-y-3">
                                @foreach ($comment->replies as $reply)
                                    <li class="border rounded-lg p-3 bg-gray-100">
                                        <div class="flex items-start gap-3">
                                            <x-user-avatar :user="$reply->user" class="w-8 h-8" />
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold">{{ $reply->user->name }}</span>
                                                    <span class="text-gray-400 text-sm">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="mt-1 text-gray-700">{{ $reply->content }}</p>

                                                <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                                                    <button type="button" onclick="toggleLike({{ $reply->id }})" class="hover:text-blue-600 flex items-center gap-1">
                                                        👍 <span id="comment-like-count-{{ $reply->id }}">{{ $reply->likes_count ?? 0 }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>
                </div>
            </li>
        @endforeach
    </ul>

</div>

<script>
    function toggleLike(commentId) {
        fetch(`/comments/${commentId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                document.getElementById(`comment-like-count-${commentId}`).textContent = data.count;
            }
        })
        .catch(console.error);
    }
</script>
