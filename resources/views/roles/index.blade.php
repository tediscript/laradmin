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
                    @if($roles->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="pb-3 font-medium text-sm text-gray-700">Name</th>
                                        <th class="pb-3 font-medium text-sm text-gray-700">Permissions</th>
                                        <th class="pb-3 font-medium text-sm text-gray-700">Users</th>
                                        <th class="pb-3 font-medium text-sm text-gray-700">Date</th>
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
                                            <td class="py-4">
                                                <span class="text-sm text-gray-600">
                                                    {{ $role->permissions->count() }} permissions
                                                </span>
                                            </td>
                                            <td class="py-4">
                                                <span class="text-sm text-gray-600">
                                                    {{ $role->users->count() }} users
                                                </span>
                                            </td>
                                            <td class="py-4 text-sm text-gray-500">
                                                {{ $role->created_at->format('M d, Y') }}
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
                        <p class="text-gray-500 text-sm">No roles found. <a href="{{ route('admin.roles.create') }}" class="text-gray-700 underline">Create your first role.</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>