<x-app-layout>
    <div class="max-w-4xl mx-auto py-8">

        <h1 class="text-2xl font-bold mb-6">
            Category: {{ $category->name }}
        </h1>

        @if ($posts->count())
            <div class="space-y-5">
                @foreach ($posts as $post)
                    <a href="{{ route('post.show', [$post->user->username, $post->slug]) }}"
                       class="block p-5 border rounded-lg hover:bg-gray-50 transition">

                        <h2 class="text-xl font-semibold">{{ $post->title }}</h2>

                        <p class="text-gray-600 text-sm mt-1">
                            {{ Str::limit($post->content, 130) }}
                        </p>

                        <p class="text-sm text-gray-500 mt-2">
                            {{ $post->created_at->format('M d, Y') }}
                        </p>

                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }}
            </div>

        @else

            <p class="text-gray-500">No posts found in this category.</p>

        @endif

    </div>
</x-app-layout>
