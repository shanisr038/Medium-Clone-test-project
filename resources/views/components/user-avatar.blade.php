@props(['user', 'size' => 'w-12 h-12'])

<img 
    src="{{ $user->avatarUrl() }}" 
    alt="{{ $user->name }}" 
    class="{{ $size }} rounded-full object-cover">
