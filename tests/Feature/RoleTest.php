<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $rolePermissions = ['role.view', 'role.create', 'role.update', 'role.delete'];
    foreach ($rolePermissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $role->givePermissionTo($rolePermissions);
    $this->admin->assignRole($role);
});

test('roles index is displayed', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles');

    $response->assertOk();
});

test('role create page is displayed', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles/create');

    $response->assertOk();
});

test('role can be created', function () {
    Permission::firstOrCreate(['name' => 'post.view']);

    $response = $this
        ->actingAs($this->admin)
        ->post('/admin/roles', [
            'name' => 'manager',
            'permissions' => ['post.view'],
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/roles');

    $role = Role::where('name', 'manager')->first();
    $this->assertNotNull($role);
    $this->assertTrue($role->hasPermissionTo('post.view'));
});

test('role can be updated', function () {
    $role = Role::firstOrCreate(['name' => 'editor']);
    $permission = Permission::firstOrCreate(['name' => 'post.view']);
    $role->givePermissionTo($permission);

    $response = $this
        ->actingAs($this->admin)
        ->put("/admin/roles/{$role->id}", [
            'name' => 'editor-updated',
            'permissions' => ['post.view'],
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/roles');

    $role->refresh();
    $this->assertSame('editor-updated', $role->name);
    $this->assertTrue($role->hasPermissionTo('post.view'));
});

test('role permissions can be synced', function () {
    $role = Role::firstOrCreate(['name' => 'viewer']);
    $permission = Permission::firstOrCreate(['name' => 'post.view']);
    $role->givePermissionTo($permission);

    Permission::firstOrCreate(['name' => 'post.create']);

    $response = $this
        ->actingAs($this->admin)
        ->put("/admin/roles/{$role->id}", [
            'name' => 'viewer',
            'permissions' => ['post.create'],
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/roles');

    $role->refresh();
    $this->assertFalse($role->hasPermissionTo('post.view'));
    $this->assertTrue($role->hasPermissionTo('post.create'));
});

test('role can be deleted', function () {
    $role = Role::firstOrCreate(['name' => 'temp-role']);

    $response = $this
        ->actingAs($this->admin)
        ->delete("/admin/roles/{$role->id}");

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/roles');

    $this->assertNull(Role::where('name', 'temp-role')->first());
});

test('role name must be unique on create', function () {
    Role::firstOrCreate(['name' => 'duplicate']);

    $response = $this
        ->actingAs($this->admin)
        ->post('/admin/roles', [
            'name' => 'duplicate',
        ]);

    $response->assertSessionHasErrors('name');
});

test('role edit page is displayed', function () {
    $role = Role::firstOrCreate(['name' => 'editor']);

    $response = $this
        ->actingAs($this->admin)
        ->get("/admin/roles/{$role->id}/edit");

    $response->assertOk();
});
