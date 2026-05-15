<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Roles
            </h2>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                Add Role
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-4 p-4 bg-white rounded-lg shadow-sm text-sm text-gray-700 font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php
                        $sortParams = array_filter([
                            'sort_by' => $sortBy,
                            'sort_direction' => $sortDirection,
                        ]);
                    @endphp

                    {{-- Search Input --}}
                    <div class="mb-4">
                        <form method="GET" action="{{ route('admin.roles.index') }}" class="flex gap-3 items-center">
                            <div class="relative flex-1 max-w-sm">
                                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Search by name..."
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-gray-700 focus:border-gray-700 pl-9 pr-3 py-2 border"
                                >
                            </div>
                            @foreach($sortParams as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700 transition duration-150">
                                Search
                            </button>
                            @if($search)
                                <a href="{{ route('admin.roles.index', $sortParams) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-50 transition duration-150">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>

                    @if($roles->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        @php
                                            $columns = [
                                                'name' => 'Name',
                                                'created_at' => 'Date',
                                                'permissions_count' => 'Permissions',
                                                'users_count' => 'Users',
                                            ];
                                        @endphp

                                        @foreach($columns as $column => $label)
                                            @php
                                                $isActive = $sortBy === $column;
                                                $newDirection = $isActive && $sortDirection === 'asc' ? 'desc' : 'asc';
                                                $linkParams = array_filter([
                                                    'sort_by' => $column,
                                                    'sort_direction' => $newDirection,
                                                    'search' => $search,
                                                ]);
                                            @endphp
                                            <th class="pb-3 font-medium text-sm text-gray-700">
                                                <a href="{{ route('admin.roles.index', $linkParams) }}" class="inline-flex items-center gap-1 hover:text-gray-900 {{ $isActive ? 'text-gray-900' : '' }}">
                                                    {{ $label }}
                                                    @if($isActive)
                                                        <span class="text-xs">
                                                            @if($sortDirection === 'asc')
                                                                ↑
                                                            @else
                                                                ↓
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-400">⇅</span>
                                                    @endif
                                                </a>
                                            </th>
                                        @endforeach
                                        <th class="pb-3 font-medium text-sm text-gray-700 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr class="border-b border-gray-100 last:border-0">
                                            <td class="py-4">
                                                <span class="text-gray-900 font-medium">
                                                    {{ $role->name }}
                                                </span>
                                            </td>
                                            <td class="py-4 text-sm text-gray-500">
                                                {{ $role->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="py-4">
                                                <span class="text-sm text-gray-600">
                                                    {{ $role->permissions_count }} permissions
                                                </span>
                                            </td>
                                            <td class="py-4">
                                                <span class="text-sm text-gray-600">
                                                    {{ $role->users_count }} users
                                                </span>
                                            </td>
                                            <td class="py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white hover:bg-red-500 active:bg-red-700 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $roles->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">
                            @if($search)
                                No roles found matching "{{ $search }}". <a href="{{ route('admin.roles.index', $sortParams) }}" class="text-gray-700 underline">Clear search.</a>
                            @else
                                No roles found. <a href="{{ route('admin.roles.create') }}" class="text-gray-700 underline">Create your first role.</a>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>