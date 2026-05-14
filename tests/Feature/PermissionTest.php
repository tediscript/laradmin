<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $permissionPermissions = ['permission.view', 'permission.create', 'permission.update', 'permission.delete'];
    foreach ($permissionPermissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $role->givePermissionTo($permissionPermissions);
    $this->admin->assignRole($role);
});

test('permissions index is displayed', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions');

    $response->assertOk();
});

test('permission create page is displayed', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions/create');

    $response->assertOk();
});

test('permission can be created', function () {
    $response = $this
        ->actingAs($this->admin)
        ->post('/admin/permissions', [
            'name' => 'category.view',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/permissions');

    $permission = Permission::where('name', 'category.view')->first();
    $this->assertNotNull($permission);
});

test('permission can be updated', function () {
    $permission = Permission::firstOrCreate(['name' => 'category.view']);

    $response = $this
        ->actingAs($this->admin)
        ->put("/admin/permissions/{$permission->id}", [
            'name' => 'category.list',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/permissions');

    $permission->refresh();
    $this->assertSame('category.list', $permission->name);
});

test('permission can be deleted', function () {
    $permission = Permission::firstOrCreate(['name' => 'temp.permission']);

    $response = $this
        ->actingAs($this->admin)
        ->delete("/admin/permissions/{$permission->id}");

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/permissions');

    $this->assertNull(Permission::where('name', 'temp.permission')->first());
});

test('permission name must be unique on create', function () {
    Permission::firstOrCreate(['name' => 'duplicate.perm']);

    $response = $this
        ->actingAs($this->admin)
        ->post('/admin/permissions', [
            'name' => 'duplicate.perm',
        ]);

    $response->assertSessionHasErrors('name');
});

test('permission name must be unique on update except self', function () {
    Permission::firstOrCreate(['name' => 'existing.perm']);
    $permission = Permission::firstOrCreate(['name' => 'current.perm']);

    // Updating to same name should work
    $response = $this
        ->actingAs($this->admin)
        ->put("/admin/permissions/{$permission->id}", [
            'name' => 'current.perm',
        ]);

    $response->assertSessionHasNoErrors();

    // Updating to existing name should fail
    $response = $this
        ->actingAs($this->admin)
        ->put("/admin/permissions/{$permission->id}", [
            'name' => 'existing.perm',
        ]);

    $response->assertSessionHasErrors('name');
});

test('permission edit page is displayed', function () {
    $permission = Permission::firstOrCreate(['name' => 'test.edit']);

    $response = $this
        ->actingAs($this->admin)
        ->get("/admin/permissions/{$permission->id}/edit");

    $response->assertOk();
});
