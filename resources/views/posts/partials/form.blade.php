@props(['post'])

<div class="space-y-6">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input
            id="title"
            name="title"
            type="text"
            class="mt-1 block w-full"
            :value="old('title', $post->title ?? '')"
            required
            autofocus
        />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="body" value="Body" />
        <textarea
            id="body"
            name="body"
            rows="6"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
        >{{ old('body', $post->body ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('body')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="published" value="0" />
        <input
            type="checkbox"
            id="published"
            name="published"
            value="1"
            class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
            {{ old('published', isset($post) ? $post->published : false) ? 'checked' : '' }}
        />
        <x-input-label for="published" value="Published" />
    </div>
</div>