<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user without password sees set password heading on profile', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    $response = $this->actingAs($user)->get('/admin/profile');

    $response->assertStatus(200);
    $response->assertSee('Set Password');
    $response->assertDontSee('Current Password');
});

test('user with password sees update password heading on profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/profile');

    $response->assertStatus(200);
    $response->assertSee('Update Password');
    $response->assertSee('Current Password');
});

test('user without password can set password without current password', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
});

test('user without password gets validation error when providing current password field', function () {
    // This test verifies that current_password is NOT required when user has no password
    $user = User::factory()->create([
        'password' => null,
    ]);

    // Should succeed without current_password at all
    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ]);

    $response->assertSessionHasNoErrors();
    $this->assertTrue(Hash::check('brand-new-password', $user->refresh()->password));
});

test('user with password must provide current password to update', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});

test('user with wrong current password gets validation error', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});

test('after setting password form switches to update mode', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    // Set password
    $this
        ->actingAs($user)
        ->put('/password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    // Now profile should show Update Password with Current Password field
    $response = $this->actingAs($user->refresh())->get('/admin/profile');

    $response->assertStatus(200);
    $response->assertSee('Update Password');
    $response->assertSee('Current Password');
});

test('new password must be confirmed', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'password')
        ->assertRedirect('/profile');
});
