@props(['user'])

<div class="space-y-6">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input
            id="name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            :value="old('name', $user->name ?? '')"
            required
            autofocus
        />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input
            id="email"
            name="email"
            type="email"
            class="mt-1 block w-full"
            :value="old('email', $user->email ?? '')"
            required
        />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password" value="{{ isset($user) ? 'New Password' : 'Password' }}" />
        <input
            id="password"
            name="password"
            type="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
            @if(!isset($user)) required @endif
        />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
        @if(isset($user))
            <p class="mt-1 text-xs text-gray-500">Leave blank to keep the current password.</p>
        @endif
    </div>

    <div>
        <x-input-label for="password_confirmation" value="Confirm Password" />
        <input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
        />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="timezone" value="Timezone" />
        <select id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-700 focus:border-gray-700 text-sm">
            @php
                $groupedTimezones = [];
                foreach (DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $tz) {
                    $parts = explode('/', $tz);
                    $region = $parts[0];
                    $groupedTimezones[$region][] = $tz;
                }
                ksort($groupedTimezones);
                $selectedTimezone = old('timezone', isset($user) && $user->timezone ? $user->timezone : '');
            @endphp
            <option value="" {{ ! $selectedTimezone ? 'selected' : '' }}>
                {{ config('app.timezone') }} (default)
            </option>
            @foreach ($groupedTimezones as $region => $timezones)
                <optgroup label="{{ $region }}">
                    @foreach ($timezones as $tz)
                        <option value="{{ $tz }}" {{ $selectedTimezone === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <input
            type="checkbox"
            id="email_verified"
            name="email_verified"
            value="1"
            class="rounded border-gray-300 text-gray-800 shadow-sm focus:ring-gray-700"
            @if(old('email_verified', isset($user) && $user->email_verified_at ? '1' : '')) checked @endif
        />
        <x-input-label for="email_verified" value="Email Verified" />
    </div>
</div>
