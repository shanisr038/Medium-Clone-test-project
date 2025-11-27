<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Posts') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($posts->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($posts as $post)
                                <div class="border rounded-lg overflow-hidden shadow hover:shadow-md transition">
                                    @if($post->getFirstMedia('image'))
                                        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold mb-2">
                                            <a href="{{ route('post.show', [$post->user->username, $post->slug]) }}"
                                               class="text-indigo-600 hover:text-indigo-800">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            Category: {{ $post->category->name ?? 'Uncategorized' }}
                                        </p>
                                        <div class="flex justify-between items-center text-sm text-gray-500">
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('posts.edit', $post) }}" class="text-blue-500 hover:underline">Edit</a>
                                                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <p class="text-gray-600 text-center">You haven’t created any posts yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
