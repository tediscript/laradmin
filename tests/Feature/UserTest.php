<?php

use App\Models\User;

test('guests cannot access users', function () {
    $this->get('/admin/users')->assertRedirect('/login');
    $this->get('/admin/users/create')->assertRedirect('/login');
    $this->post('/admin/users')->assertRedirect('/login');
});

test('index displays users table', function () {
    $user = User::factory()->create();
    User::factory()->count(3)->create();

    $response = $this->actingAs($user)->get('/admin/users');

    $response->assertOk();
    $response->assertSee('Users');
});

test('create displays the form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/users/create');

    $response->assertOk();
    $response->assertSee('Create User');
});

test('store creates a new user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('status', 'User created successfully.');

    $newUser = User::where('email', 'john@example.com')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->name)->toBe('John Doe');
    expect($newUser->email)->toBe('john@example.com');
});

test('store validates required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin/users', []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

test('store validates unique email', function () {
    $user = User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->actingAs($user)->post('/admin/users', [
        'name' => 'New User',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('store validates password confirmation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('show displays user detail', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['name' => 'Jane Doe']);

    $response = $this->actingAs($user)->get("/admin/users/{$targetUser->id}");

    $response->assertOk();
    $response->assertSee('Jane Doe');
});

test('edit displays the form with user data', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['name' => 'Edit Me']);

    $response = $this->actingAs($user)->get("/admin/users/{$targetUser->id}/edit");

    $response->assertOk();
    $response->assertSee('Edit User');
    $response->assertSee('Edit Me');
});

test('update modifies the user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    $response = $this->actingAs($user)->put("/admin/users/{$targetUser->id}", [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => '',
    ]);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('status', 'User updated successfully.');

    $targetUser->refresh();
    expect($targetUser->name)->toBe('Updated Name');
    expect($targetUser->email)->toBe('updated@example.com');
});

test('update can change password', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $oldPasswordHash = $targetUser->password;

    $response = $this->actingAs($user)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect('/admin/users');

    $targetUser->refresh();
    expect($targetUser->password)->not->toBe($oldPasswordHash);
});

test('store creates verified user when email_verified is checked', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin/users', [
        'name' => 'Verified User',
        'email' => 'verified@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'email_verified' => '1',
    ]);

    $response->assertRedirect('/admin/users');

    $newUser = User::where('email', 'verified@example.com')->first();
    expect($newUser->email_verified_at)->not->toBeNull();
});

test('store creates unverified user when email_verified is unchecked', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/admin/users', [
        'name' => 'Unverified User',
        'email' => 'unverified@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $newUser = User::where('email', 'unverified@example.com')->first();
    expect($newUser->email_verified_at)->toBeNull();
});

test('update can verify an unverified user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['email_verified_at' => null]);

    $this->actingAs($user)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
        'email_verified' => '1',
    ]);

    $targetUser->refresh();
    expect($targetUser->email_verified_at)->not->toBeNull();
});

test('update can unverify a verified user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
    ]);

    $targetUser->refresh();
    expect($targetUser->email_verified_at)->toBeNull();
});

test('destroy deletes the user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    $response = $this->actingAs($user)->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('status', 'User deleted successfully.');

    expect(User::find($targetUser->id))->toBeNull();
});
