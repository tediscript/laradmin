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

test('permissions can be sorted by name ascending', function () {
    Permission::firstOrCreate(['name' => 'beta.perm']);
    Permission::firstOrCreate(['name' => 'alpha.perm']);
    Permission::firstOrCreate(['name' => 'gamma.perm']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions?sort_by=name&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder(['alpha.perm', 'beta.perm', 'gamma.perm']);
});

test('permissions can be sorted by name descending', function () {
    Permission::firstOrCreate(['name' => 'beta.perm']);
    Permission::firstOrCreate(['name' => 'alpha.perm']);
    Permission::firstOrCreate(['name' => 'gamma.perm']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions?sort_by=name&sort_direction=desc');

    $response->assertOk();
    $response->assertSeeInOrder(['gamma.perm', 'beta.perm', 'alpha.perm']);
});

test('permissions can be searched by name', function () {
    Permission::firstOrCreate(['name' => 'post.view']);
    Permission::firstOrCreate(['name' => 'user.create']);
    Permission::firstOrCreate(['name' => 'role.delete']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions?search=post');

    $response->assertOk();
    $response->assertSee('post.view');
    $response->assertDontSee('user.create');
    $response->assertDontSee('role.delete');
});

test('permissions search with no results shows message', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions?search=nonexistent');

    $response->assertOk();
    $response->assertSee('No permissions found matching');
});

test('permissions can be sorted by roles count ascending', function () {
    $permA = Permission::create(['name' => 'sort.perm-a']);
    $permB = Permission::create(['name' => 'sort.perm-b']);
    $permC = Permission::create(['name' => 'sort.perm-c']);

    $role1 = Role::create(['name' => 'sort-role-1']);
    $role2 = Role::create(['name' => 'sort-role-2']);
    $role3 = Role::create(['name' => 'sort-role-3']);

    $permA->assignRole([$role1, $role2, $role3]);
    $permB->assignRole([$role1]);
    $permC->assignRole([$role1, $role2]);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions?sort_by=roles_count&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder(['sort.perm-b', 'sort.perm-c', 'sort.perm-a']);
});

test('permissions can be sorted by roles count descending', function () {
    $permA = Permission::create(['name' => 'sort.perm-a']);
    $permB = Permission::create(['name' => 'sort.perm-b']);
    $permC = Permission::create(['name' => 'sort.perm-c']);

    $role1 = Role::create(['name' => 'sort-role-1']);
    $role2 = Role::create(['name' => 'sort-role-2']);
    $role3 = Role::create(['name' => 'sort-role-3']);

    $permA->assignRole([$role1, $role2, $role3]);
    $permB->assignRole([$role1]);
    $permC->assignRole([$role1, $role2]);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions?sort_by=roles_count&sort_direction=desc');

    $response->assertOk();
    $response->assertSeeInOrder(['sort.perm-a', 'sort.perm-c', 'sort.perm-b']);
});

test('permissions invalid sort column defaults to created_at', function () {
    Permission::firstOrCreate(['name' => 'test.sort']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/permissions?sort_by=invalid_column&sort_direction=asc');

    $response->assertOk();
    $response->assertSee('test.sort');
});
