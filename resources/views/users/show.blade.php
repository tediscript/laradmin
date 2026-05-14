<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $user->name }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                    Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Name</h3>
                        <p class="mt-1 text-gray-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                        <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>{{ $user->created_at->format('M d, Y') }}</span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-500">
                                Unverified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>