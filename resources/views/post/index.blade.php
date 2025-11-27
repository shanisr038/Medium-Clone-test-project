<x-app-layout>
    <div class="max-w-6xl mx-auto py-8 px-4">

        {{-- 🏷️ Category Tabs --}}
        <ul class="flex flex-wrap mb-6 border-b pb-2 justify-center">
            <li class="me-2">
                <a href="{{ route('dashboard') }}"
                   class="inline-block px-4 py-2 rounded-lg 
                          {{ !isset($category) ? 'bg-blue-600 text-white' : 'hover:text-gray-900 hover:bg-gray-100' }}">
                    All
                </a>
            </li>

            @foreach ($categories as $cat)
                <li class="me-2">
                    <a href="{{ route('category.show', $cat->slug) }}"
                       class="inline-block px-4 py-2 rounded-lg 
                              {{ isset($category) && $category->id === $cat->id 
                                  ? 'bg-blue-600 text-white' 
                                  : 'hover:text-gray-900 hover:bg-gray-100' }}">
                        {{ $cat->name }}
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- 📰 Posts Section --}}
        <div class="space-y-6">
            @forelse ($posts as $post)
                <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-200 flex flex-col md:flex-row-reverse items-start gap-4">

                    {{-- 🖼️ Post Image --}}
                    @if ($post->getFirstMedia('image'))
                        <a href="{{ route('post.show', [$post->user->username, $post->slug]) }}" class="block w-full md:w-1/3">
                            <img src="{{ $post->imageUrl() }}" 
                                 alt="{{ $post->title }}" 
                                 class="rounded-md w-full h-48 object-cover hover:opacity-90 transition duration-200">
                        </a>
                    @endif

                    {{-- 📄 Post Text --}}
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold mb-2">
                            <a href="{{ route('post.show', [$post->user->username, $post->slug]) }}" 
                               class="hover:underline text-gray-800">
                                {{ $post->title }}
                            </a>
                        </h2>

                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            {{ Str::limit(strip_tags($post->content), 120) }}
                        </p>

                        {{-- ⏰ Created Time + 👍 Like Button --}}
                        <div class="flex items-center justify-between text-sm text-gray-500 mt-3">
                            <div class="flex items-center gap-3">
                                <span>By 
                                    <a href="{{ route('profile.show', $post->user->username) }}" 
                                       class="text-blue-500 hover:underline">
                                       {{ $post->user->name }}
                                    </a>
                                </span>
                                • 
                                <span>{{ $post->created_at->diffForHumans() }}</span>

                                {{-- 👍 Like Button --}}
                                @auth
                                    <button 
                                        class="like-btn flex items-center gap-1 text-gray-600 hover:text-blue-600 transition-colors duration-200" 
                                        data-post-id="{{ $post->id }}"
                                        data-liked="{{ $post->isClappedBy(auth()->user()) ? 'true' : 'false' }}"
                                        type="button">
                                        
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             fill="{{ $post->isClappedBy(auth()->user()) ? '#2563eb' : 'none' }}"
                                             stroke="{{ $post->isClappedBy(auth()->user()) ? '#2563eb' : 'currentColor' }}"
                                             stroke-width="2"
                                             class="w-5 h-5 transition-all duration-200">
                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                  d="M2 21h4V9H2v12zM23 10c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 2 7.59 8.59C7.22 8.95 7 9.45 7 10v9c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-.01z"/>
                                        </svg>

                                        <span class="like-count">{{ $post->claps()->count() }}</span>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="flex items-center gap-1 text-gray-600 hover:text-blue-600 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                  d="M2 21h4V9H2v12zM23 10c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 2 7.59 8.59C7.22 8.95 7 9.45 7 10v9c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-.01z"/>
                                        </svg>
                                        <span>{{ $post->claps()->count() }}</span>
                                    </a>
                                @endauth
                            </div>

                            <div>
                                <span>{{ $post->readtime() }} min read</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">No posts found.</p>
            @endforelse
        </div>

        {{-- 📄 Pagination --}}
        <div class="mt-8">
            {{ $posts->links('pagination::tailwind') }}
        </div>

    </div>

    {{-- ⚡ Like Toggle Script --}}
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.like-btn').forEach(button => {
                    button.addEventListener('click', async function(e) {
                        e.preventDefault();
                        
                        const postId = this.dataset.postId;
                        const icon = this.querySelector('svg');
                        const countSpan = this.querySelector('.like-count');
                        const isCurrentlyLiked = this.dataset.liked === 'true';

                        // Optimistic UI update
                        const currentCount = parseInt(countSpan.textContent);
                        if (isCurrentlyLiked) {
                            // Unlike: decrease count
                            countSpan.textContent = currentCount - 1;
                            icon.setAttribute('fill', 'none');
                            icon.setAttribute('stroke', 'currentColor');
                            this.dataset.liked = 'false';
                        } else {
                            // Like: increase count
                            countSpan.textContent = currentCount + 1;
                            icon.setAttribute('fill', '#2563eb');
                            icon.setAttribute('stroke', '#2563eb');
                            this.dataset.liked = 'true';
                        }

                        // Add animation
                        icon.classList.add('scale-125');
                        setTimeout(() => icon.classList.remove('scale-125'), 200);

                        try {
                            const response = await fetch(`/posts/${postId}/clap`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin'
                            });

                            const data = await response.json();

                            if (data.status !== 'success') {
                                // Revert UI if API call failed
                                if (isCurrentlyLiked) {
                                    countSpan.textContent = currentCount;
                                    icon.setAttribute('fill', '#2563eb');
                                    icon.setAttribute('stroke', '#2563eb');
                                    this.dataset.liked = 'true';
                                } else {
                                    countSpan.textContent = currentCount;
                                    icon.setAttribute('fill', 'none');
                                    icon.setAttribute('stroke', 'currentColor');
                                    this.dataset.liked = 'false';
                                }
                                console.error('Like action failed:', data);
                            }
                            
                            // Update with actual count from server (in case of race conditions)
                            countSpan.textContent = data.count;
                            
                        } catch (error) {
                            console.error('Network error:', error);
                            // Revert UI on network error
                            if (isCurrentlyLiked) {
                                countSpan.textContent = currentCount;
                                icon.setAttribute('fill', '#2563eb');
                                icon.setAttribute('stroke', '#2563eb');
                                this.dataset.liked = 'true';
                            } else {
                                countSpan.textContent = currentCount;
                                icon.setAttribute('fill', 'none');
                                icon.setAttribute('stroke', 'currentColor');
                                this.dataset.liked = 'false';
                            }
                        }
                    });
                });
            });
        </script>
    @endauth
</x-app-layout>