<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex flex-col md:flex-row">
                    <!-- Left Section: Posts -->
                    <div class="flex-1 md:pr-8">
                        <h1 class="text-4xl font-bold text-gray-800">{{ $user->name }}</h1>

                        <div class="mt-8 space-y-6">
                            @forelse ($posts as $post)
                                <x-post-item :post="$post" />
                            @empty
                                <div class="text-center text-gray-400 py-16">
                                    No posts available
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right Sidebar: Profile Info -->
                    <div class="w-full md:w-[320px] border-l border-gray-200 md:pl-8 mt-8 md:mt-0">
                        <div class="text-center">
                            {{-- Use avatarUrl() helper --}}
                            <x-user-avatar :user="$user" size="w-24 h-24 mx-auto" />

                            <h3 class="text-xl font-semibold mt-4 text-gray-800">{{ $user->name }}</h3>

                            {{-- Follower Count --}}
                            <p class="text-gray-500 mt-1">
                                {{ $user->followersCount() }} follower{{ $user->followersCount() !== 1 ? 's' : '' }}
                            </p>

                            {{-- User Bio --}}
                            @if ($user->bio)
                                <p class="mt-2 text-gray-600 text-sm leading-relaxed">
                                    {{ $user->bio }}
                                </p>
                            @endif

                            {{-- Follow / Unfollow Logic --}}
                            @auth
                                @if (Auth::id() !== $user->id)
                                    <form method="POST" action="{{ route('user.toggleFollow', $user) }}">
                                        @csrf

                                        @if ($user->isFollowedBy(Auth::user()))
                                            <button
                                                type="submit"
                                                class="bg-red-600 hover:bg-red-700 rounded-full px-5 py-2 text-white mt-4 font-medium transition">
                                                Unfollow
                                            </button>
                                        @else
                                            <button
                                                type="submit"
                                                class="bg-emerald-600 hover:bg-emerald-700 rounded-full px-5 py-2 text-white mt-4 font-medium transition">
                                                Follow
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('register') }}"
                                    class="inline-block bg-emerald-600 hover:bg-emerald-700 rounded-full px-5 py-2 text-white mt-4 font-medium transition">
                                    Follow
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
