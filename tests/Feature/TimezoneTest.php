<?php

use App\Models\User;
use Illuminate\Support\Facades\Blade;

test('user display timezone returns user preference when set', function () {
    $user = User::factory()->create(['timezone' => 'Asia/Jakarta']);

    expect($user->displayTimezone())->toBe('Asia/Jakarta');
});

test('user display timezone falls back to config when not set', function () {
    $user = User::factory()->create(['timezone' => null]);

    expect($user->displayTimezone())->toBe(config('app.timezone'));
});

test('timezone can be updated via profile', function () {
    $user = User::factory()->create(['timezone' => null]);

    $response = $this->actingAs($user)->patch('/admin/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'timezone' => 'Asia/Jakarta',
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect('/admin/profile');

    expect($user->refresh()->timezone)->toBe('Asia/Jakarta');
});

test('timezone can be cleared to use default', function () {
    $user = User::factory()->create(['timezone' => 'Asia/Jakarta']);

    $response = $this->actingAs($user)->patch('/admin/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'timezone' => '',
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect('/admin/profile');

    expect($user->refresh()->timezone)->toBeNull();
});

test('timezone validation rejects invalid timezone', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch('/admin/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'timezone' => 'Invalid/Timezone',
    ]);

    $response->assertSessionHasErrors(['timezone']);
});

test('new user has null timezone', function () {
    $user = User::factory()->create();

    expect($user->timezone)->toBeNull();
    expect($user->displayTimezone())->toBe(config('app.timezone'));
});

test('datetime blade directive converts to user timezone', function () {
    $user = User::factory()->create(['timezone' => 'Asia/Jakarta']);
    $this->actingAs($user);

    $compiled = Blade::compileString('@datetime($date)');
    expect($compiled)->toContain('setTimezone');
    expect($compiled)->toContain('displayTimezone');
});
