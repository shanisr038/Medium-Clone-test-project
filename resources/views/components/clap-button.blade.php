@props(['post'])

<div class="flex items-center gap-2">
    <button id="clap-btn-{{ $post->id }}" onclick="toggleClap({{ $post->id }})"
        class="px-3 py-1.5 rounded-md text-white {{ auth()->check() && $post->isClappedBy(auth()->user()) ? 'bg-blue-600' : 'bg-gray-400' }} hover:bg-blue-700 transition">
        👏 Clap
    </button>
    <span id="clap-count-{{ $post->id }}">{{ $post->claps()->count() }}</span>
</div>

<script>
    async function toggleClap(postId){
        const res = await fetch(`/posts/${postId}/clap`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept':'application/json'
            }
        });
        const data = await res.json();
        if(data.success){
            const btn = document.getElementById(`clap-btn-${postId}`);
            const count = document.getElementById(`clap-count-${postId}`);
            if(data.clapped){
                btn.classList.remove('bg-gray-400');
                btn.classList.add('bg-blue-600');
            } else {
                btn.classList.remove('bg-blue-600');
                btn.classList.add('bg-gray-400');
            }
            count.textContent = data.count;
        }
    }
</script>
