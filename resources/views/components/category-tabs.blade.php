<ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 justify-center border-b pb-2">
    {{-- "All" tab --}}
    <li class="me-2">
        <a href="{{ route('dashboard') }}"
           class="inline-block px-4 py-2 rounded-lg transition-colors duration-200
                  {{ !isset($activeCategory) 
                      ? 'bg-blue-600 text-white' 
                      : 'hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white' }}">
            All
        </a>
    </li>

    {{-- Dynamic category tabs --}}
    @foreach ($categories as $category)
        <li class="me-2">
            <a href="{{ route('category.show', $category->slug) }}"
               class="inline-block px-4 py-2 rounded-lg transition-colors duration-200
                      {{ isset($activeCategory) && $activeCategory->id === $category->id
                          ? 'bg-blue-600 text-white'
                          : 'hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white' }}">
                {{ $category->name }}
            </a>
        </li>
    @endforeach
</ul>
