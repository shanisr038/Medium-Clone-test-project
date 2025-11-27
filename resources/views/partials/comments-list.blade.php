@if($comments->count() > 0)
<ul class="space-y-4">
    @foreach($comments as $comment)
        @include('partials.comments-item', ['comment' => $comment])
    @endforeach
</ul>
@else
<p class="text-gray-500">No comments yet.</p>
@endif
