<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $userPermissions = ['user.view', 'user.create', 'user.update', 'user.delete'];
    foreach ($userPermissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $role->givePermissionTo($userPermissions);
    $this->admin->assignRole($role);
});

test('guests cannot access users', function () {
    $this->get('/admin/users')->assertRedirect('/login');
    $this->get('/admin/users/create')->assertRedirect('/login');
    $this->post('/admin/users')->assertRedirect('/login');
});

test('index displays users table', function () {
    User::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)->get('/admin/users');

    $response->assertOk();
    $response->assertSee('Users');
});

test('create displays the form', function () {
    $response = $this->actingAs($this->admin)->get('/admin/users/create');

    $response->assertOk();
    $response->assertSee('Create User');
});

test('store creates a new user', function () {
    $response = $this->actingAs($this->admin)->post('/admin/users', [
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
    $response = $this->actingAs($this->admin)->post('/admin/users', []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

test('store validates unique email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'New User',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('store validates password confirmation', function () {
    $response = $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('show displays user detail', function () {
    $targetUser = User::factory()->create(['name' => 'Jane Doe']);

    $response = $this->actingAs($this->admin)->get("/admin/users/{$targetUser->id}");

    $response->assertOk();
    $response->assertSee('Jane Doe');
});

test('edit displays the form with user data', function () {
    $targetUser = User::factory()->create(['name' => 'Edit Me']);

    $response = $this->actingAs($this->admin)->get("/admin/users/{$targetUser->id}/edit");

    $response->assertOk();
    $response->assertSee('Edit User');
    $response->assertSee('Edit Me');
});

test('edit displays roles checkboxes', function () {
    $targetUser = User::factory()->create();
    Role::create(['name' => 'editor']);
    Role::create(['name' => 'manager']);

    $response = $this->actingAs($this->admin)->get("/admin/users/{$targetUser->id}/edit");

    $response->assertSee('editor');
    $response->assertSee('manager');
});

test('update can assign roles to user', function () {
    $targetUser = User::factory()->create();
    Role::create(['name' => 'editor']);
    Role::create(['name' => 'manager']);

    $response = $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
        'roles' => ['editor', 'manager'],
    ]);

    $response->assertRedirect('/admin/users');

    $targetUser->refresh();
    expect($targetUser->hasRole('editor'))->toBeTrue();
    expect($targetUser->hasRole('manager'))->toBeTrue();
});

test('update can sync roles for user', function () {
    $targetUser = User::factory()->create();
    Role::create(['name' => 'editor']);
    $managerRole = Role::create(['name' => 'manager']);
    $targetUser->assignRole($managerRole);

    $response = $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
        'roles' => ['editor'],
    ]);

    $response->assertRedirect('/admin/users');

    $targetUser->refresh();
    expect($targetUser->hasRole('editor'))->toBeTrue();
    expect($targetUser->hasRole('manager'))->toBeFalse();
});

test('update can remove all roles from user', function () {
    $targetUser = User::factory()->create();
    $editorRole = Role::create(['name' => 'editor']);
    $targetUser->assignRole($editorRole);

    $response = $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
        'roles' => [],
    ]);

    $response->assertRedirect('/admin/users');

    $targetUser->refresh();
    expect($targetUser->roles)->toBeEmpty();
});

test('update modifies the user', function () {
    $targetUser = User::factory()->create();

    $response = $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
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
    $targetUser = User::factory()->create();
    $oldPasswordHash = $targetUser->password;

    $response = $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
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
    $response = $this->actingAs($this->admin)->post('/admin/users', [
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
    $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'Unverified User',
        'email' => 'unverified@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $newUser = User::where('email', 'unverified@example.com')->first();
    expect($newUser->email_verified_at)->toBeNull();
});

test('update can verify an unverified user', function () {
    $targetUser = User::factory()->create(['email_verified_at' => null]);

    $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
        'email_verified' => '1',
    ]);

    $targetUser->refresh();
    expect($targetUser->email_verified_at)->not->toBeNull();
});

test('update can unverify a verified user', function () {
    $targetUser = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
    ]);

    $targetUser->refresh();
    expect($targetUser->email_verified_at)->toBeNull();
});

test('destroy deletes the user', function () {
    $targetUser = User::factory()->create();

    $response = $this->actingAs($this->admin)->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('status', 'User deleted successfully.');

    expect(User::find($targetUser->id))->toBeNull();
});

test('store creates user with timezone', function () {
    $response = $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'TZ User',
        'email' => 'tz@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'timezone' => 'Asia/Jakarta',
    ]);

    $response->assertRedirect('/admin/users');

    $newUser = User::where('email', 'tz@example.com')->first();
    expect($newUser->timezone)->toBe('Asia/Jakarta');
});

test('store creates user without timezone', function () {
    $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'No TZ User',
        'email' => 'notz@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $newUser = User::where('email', 'notz@example.com')->first();
    expect($newUser->timezone)->toBeNull();
});

test('store validates invalid timezone', function () {
    $response = $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'Bad TZ',
        'email' => 'badtz@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'timezone' => 'Invalid/Timezone',
    ]);

    $response->assertSessionHasErrors(['timezone']);
});

test('update can change timezone', function () {
    $targetUser = User::factory()->create(['timezone' => null]);

    $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
        'timezone' => 'America/New_York',
    ]);

    $targetUser->refresh();
    expect($targetUser->timezone)->toBe('America/New_York');
});

test('update can clear timezone', function () {
    $targetUser = User::factory()->create(['timezone' => 'Asia/Jakarta']);

    $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => '',
        'timezone' => '',
    ]);

    $targetUser->refresh();
    expect($targetUser->timezone)->toBeNull();
});

test('index displays timezone column', function () {
    User::factory()->create(['timezone' => 'Asia/Jakarta']);

    $response = $this->actingAs($this->admin)->get('/admin/users');

    $response->assertOk();
    $response->assertSee('Asia/Jakarta');
    $response->assertSee('Timezone');
});

test('create form displays timezone selector', function () {
    $response = $this->actingAs($this->admin)->get('/admin/users/create');

    $response->assertOk();
    $response->assertSee('Timezone');
    $response->assertSee(config('app.timezone').' (default)');
});

test('edit form displays timezone selector with current value', function () {
    $targetUser = User::factory()->create(['timezone' => 'Europe/London']);

    $response = $this->actingAs($this->admin)->get("/admin/users/{$targetUser->id}/edit");

    $response->assertOk();
    $response->assertSee('Europe/London');
});
