<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $post->title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                    Edit
                </a>
                <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>By {{ $post->user->name }}</span>
                        <span>&middot;</span>
                        <span>@datetime($post->created_at)</span>
                        @if($post->published)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-500">
                                Draft
                            </span>
                        @endif
                    </div>

                    @if($post->body)
                        <div class="prose max-w-none text-gray-900 whitespace-pre-line">
                            {{ $post->body }}
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No content.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>