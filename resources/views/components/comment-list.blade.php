@props(['comments'])

<ul class="space-y-4">
    @forelse($comments as $comment)
        @include('partials.comment', ['comment' => $comment])
    @empty
        <p class="text-gray-500">No comments yet. Be the first to comment!</p>
    @endforelse
</ul>
