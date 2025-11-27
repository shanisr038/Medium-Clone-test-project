<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @php
                    $isEdit = isset($post);
                    $formAction = $isEdit ? route('posts.update', $post->id) : route('posts.store');
                    $submitText = $isEdit ? 'Update Post' : 'Create Post';
                @endphp

                <h1 class="text-2xl font-semibold mb-4">{{ $submitText }}</h1>

                <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <!-- Image -->
                    <div class="mt-4">
                        <x-input-label for="image" :value="__('Image')" />
                        <input id="file_input" name="image" type="file"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                        @if($isEdit && $post->getFirstMedia('image'))
                            <p class="mt-2 text-sm text-gray-500">Current Image:</p>
                            <img src="{{ $post->imageUrl() }}" class="w-32 mt-1 rounded" alt="Current image">
                        @endif
                        
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <!-- Title -->
                    <div class="mt-4">
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="block mt-1 w-full"
                            value="{{ old('title', $isEdit ? $post->title : '') }}" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Category -->
                    <div class="mt-4">
                        <x-input-label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                            <option value="">Select a Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $isEdit ? $post->category_id : '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <!-- Content -->
                    <div class="mt-4">
                        <x-input-label for="content" :value="__('Content')" />
                        <x-input-textarea id="content" name="content" class="block mt-1 w-full">{{ old('content', $isEdit ? $post->content : '') }}</x-input-textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <!-- Submit -->
                    <x-primary-button class="mt-4">{{ $submitText }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
