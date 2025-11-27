<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ showDeleteModal: false }">

                <!-- Title & Edit/Delete Buttons -->
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $post->title }}</h1>
                    @auth
                        @if(auth()->id() === $post->user_id)
                            <div class="flex items-center gap-2">
                                <a href="{{ route('posts.edit', $post->id) }}" class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition">✏️ Edit</a>
                                <button @click="showDeleteModal = true" class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-md hover:bg-red-600 transition">🗑️ Delete</button>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- User Info -->
                <div class="flex items-start gap-4 mb-6">
                    <x-user-avatar :user="$post->user" />
                    <div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('profile.show', $post->user) }}" class="hover:underline font-semibold">{{ $post->user->name }}</a>
                        </div>
                        <p class="text-sm text-gray-600">{{ $post->user->followers()->count() }} {{ Str::plural('follower', $post->user->followers()->count()) }}</p>
                        <div class="flex gap-2 text-sm text-gray-500 mt-1">
                            <span>{{ $post->readtime() }} min read</span>
                            <span>&middot;</span>
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Image -->
                @if ($post->getFirstMedia('image'))
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full rounded-lg mb-4">
                @endif

                <!-- Content -->
                <div class="prose max-w-none text-gray-800 leading-relaxed">
                    {!! nl2br(e($post->content)) !!}
                </div>

                <!-- Category -->
                <div class="mt-8">
                    <a href="{{ route('category.show', $post->category->slug) }}"
   class="px-4 py-2 bg-gray-200 text-gray-800 rounded-xl text-sm hover:bg-gray-300 transition">
    {{ $post->category->name }}
</a>

                </div>

                <!-- Clap Button -->
                <div class="mt-6">
                    <x-clap-button :post="$post" />
                </div>

                <!-- Comments Section -->
                <div id="comments-container" class="mt-10 border-t pt-6">
                    <h2 class="text-xl font-semibold mb-4">Comments</h2>

                    @auth
                        <form id="comment-form" onsubmit="submitComment(event)" class="mb-6">
                            @csrf
                            <textarea name="content" rows="2" class="w-full border rounded-md p-2 mb-2" placeholder="Add a comment..." required></textarea>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Comment</button>
                        </form>
                    @else
                        <p class="text-gray-500">Please <a href="{{ route('login') }}" class="underline text-blue-600">login</a> to comment.</p>
                    @endauth

                    <!-- Comments List -->
                    <div id="comments-list">
                        @include('partials.comments-list', ['comments' => $comments])
                    </div>
                </div>

                <!-- Related Posts -->
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                    <div class="mt-10 border-t pt-6">
                        <h2 class="text-xl font-semibold mb-4">Related Posts</h2>
                        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
                            @foreach ($relatedPosts as $related)
                                <a href="{{ route('post.show', [$related->user->username, $related->slug]) }}" class="block border rounded-lg overflow-hidden hover:shadow-lg transition">
                                    @if ($related->getFirstMedia('image'))
                                        <img src="{{ $related->imageUrl() }}" alt="{{ $related->title }}" class="w-full h-40 object-cover">
                                    @endif
                                    <div class="p-3">
                                        <h3 class="font-semibold text-gray-800">{{ $related->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $related->created_at->format('M d, Y') }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Delete Confirmation Modal - FIXED: Added x-cloak and hidden by default -->
                <div x-show="showDeleteModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div @click.away="showDeleteModal = false" class="bg-white rounded-lg shadow-lg p-6 w-96 text-center transform transition-all">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">Delete Post?</h2>
                        <p class="text-gray-600 mb-4 text-sm">Are you sure you want to permanently delete this post? This action cannot be undone.</p>
                        <div class="flex justify-center gap-4">
                            <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">Cancel</button>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Add this style to prevent flash -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- AJAX Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        async function submitComment(e){
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);
            const postId = {{ $post->id }};

            const res = await fetch(`/posts/${postId}/comments`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrfToken},
                body: data
            });

            const json = await res.json();
            if(json.success){
                document.getElementById('comments-list').insertAdjacentHTML('afterbegin', json.comment);
                form.reset();
            }
        }

        async function submitReply(e, parentId){
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);
            data.append('parent_id', parentId);
            const postId = {{ $post->id }};

            const res = await fetch(`/posts/${postId}/comments`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrfToken},
                body: data
            });

            const json = await res.json();
            if(json.success){
                const parentLi = form.closest('li');
                let ul = parentLi.querySelector('ul');
                if(!ul){
                    ul = document.createElement('ul');
                    ul.classList.add('mt-2','ml-6','space-y-2');
                    parentLi.appendChild(ul);
                }
                ul.insertAdjacentHTML('beforeend', json.comment);
                form.reset();
                const parentData = form.closest('[x-data]').__x.$data;
                if(parentData) parentData.showReply = false;
            }
        }

        async function toggleLike(commentId){
            const res = await fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrfToken, 'Accept':'application/json'}
            });
            const json = await res.json();
            if(json.success){
                document.getElementById(`comment-like-count-${commentId}`).textContent = json.count;
            }
        }

        // Prevent any accidental modal triggers
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure modal is hidden on page load
            const modal = document.querySelector('[x-show="showDeleteModal"]');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</x-app-layout>