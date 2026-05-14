<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Permission
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.permissions.store') }}">
                @csrf

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
                                    :value="old('name')"
                                    required
                                    autofocus
                                    placeholder="e.g. category.view"
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                Save Permission
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>