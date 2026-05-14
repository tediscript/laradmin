<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Role
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                @csrf
                @method('PUT')

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="name" value="Name" />
                                <x-text-input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('name', $role->name)"
                                    required
                                    autofocus
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label value="Permissions" />

                                <div class="mt-2 space-y-4">
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ ucfirst($group) }}</h4>
                                            <div class="flex flex-wrap gap-3">
                                                @foreach($groupPermissions as $permission)
                                                    <label class="flex items-center gap-2">
                                                        <input
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->name }}"
                                                            class="rounded border-gray-300 text-gray-800 shadow-sm focus:ring-gray-700"
                                                            @if(in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray()))) checked @endif
                                                        />
                                                        <span class="text-sm text-gray-600">{{ $permission->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                Update Role
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>