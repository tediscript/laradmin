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

test('roles can be sorted by name ascending', function () {
    Role::firstOrCreate(['name' => 'beta']);
    Role::firstOrCreate(['name' => 'alpha']);
    Role::firstOrCreate(['name' => 'gamma']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?sort_by=name&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder(['alpha', 'beta', 'gamma']);
});

test('roles can be sorted by name descending', function () {
    Role::firstOrCreate(['name' => 'beta']);
    Role::firstOrCreate(['name' => 'alpha']);
    Role::firstOrCreate(['name' => 'gamma']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?sort_by=name&sort_direction=desc');

    $response->assertOk();
    $response->assertSeeInOrder(['gamma', 'beta', 'alpha']);
});

test('roles can be searched by name', function () {
    Role::firstOrCreate(['name' => 'manager']);
    Role::firstOrCreate(['name' => 'supervisor']);
    Role::firstOrCreate(['name' => 'editor']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?search=manag');

    $response->assertOk();
    $response->assertSee('manager');
    $response->assertDontSee('supervisor');
    $response->assertDontSee('editor');
});

test('roles search with no results shows message', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?search=nonexistent');

    $response->assertOk();
    $response->assertSee('No roles found matching');
});

test('roles can be sorted by permissions count ascending', function () {
    $roleA = Role::create(['name' => 'role-a']);
    $roleB = Role::create(['name' => 'role-b']);
    $roleC = Role::create(['name' => 'role-c']);

    $perm1 = Permission::firstOrCreate(['name' => 'test.perm1']);
    $perm2 = Permission::firstOrCreate(['name' => 'test.perm2']);
    $perm3 = Permission::firstOrCreate(['name' => 'test.perm3']);

    $roleA->givePermissionTo([$perm1, $perm2, $perm3]);
    $roleB->givePermissionTo([$perm1]);
    $roleC->givePermissionTo([$perm1, $perm2]);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?sort_by=permissions_count&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder(['role-b', 'role-c', 'role-a']);
});

test('roles can be sorted by permissions count descending', function () {
    $roleA = Role::create(['name' => 'role-a']);
    $roleB = Role::create(['name' => 'role-b']);
    $roleC = Role::create(['name' => 'role-c']);

    $perm1 = Permission::firstOrCreate(['name' => 'test.perm1']);
    $perm2 = Permission::firstOrCreate(['name' => 'test.perm2']);
    $perm3 = Permission::firstOrCreate(['name' => 'test.perm3']);

    $roleA->givePermissionTo([$perm1, $perm2, $perm3]);
    $roleB->givePermissionTo([$perm1]);
    $roleC->givePermissionTo([$perm1, $perm2]);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?sort_by=permissions_count&sort_direction=desc');

    $response->assertOk();
    $response->assertSeeInOrder(['role-a', 'role-c', 'role-b']);
});

test('roles can be sorted by users count ascending', function () {
    $roleA = Role::create(['name' => 'role-a']);
    $roleB = Role::create(['name' => 'role-b']);
    $roleC = Role::create(['name' => 'role-c']);

    $roleA->users()->attach(User::factory()->create());
    $roleA->users()->attach(User::factory()->create());
    $roleA->users()->attach(User::factory()->create());
    $roleB->users()->attach(User::factory()->create());
    $roleC->users()->attach(User::factory()->create());
    $roleC->users()->attach(User::factory()->create());

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?sort_by=users_count&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder(['role-b', 'role-c', 'role-a']);
});

test('roles can be sorted by users count descending', function () {
    $roleA = Role::create(['name' => 'role-a']);
    $roleB = Role::create(['name' => 'role-b']);
    $roleC = Role::create(['name' => 'role-c']);

    $roleA->users()->attach(User::factory()->create());
    $roleA->users()->attach(User::factory()->create());
    $roleA->users()->attach(User::factory()->create());
    $roleB->users()->attach(User::factory()->create());
    $roleC->users()->attach(User::factory()->create());
    $roleC->users()->attach(User::factory()->create());

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?sort_by=users_count&sort_direction=desc');

    $response->assertOk();
    $response->assertSeeInOrder(['role-a', 'role-c', 'role-b']);
});

test('roles invalid sort column defaults to created_at', function () {
    Role::firstOrCreate(['name' => 'test-role']);

    $response = $this
        ->actingAs($this->admin)
        ->get('/admin/roles?sort_by=invalid_column&sort_direction=asc');

    $response->assertOk();
    $response->assertSee('test-role');
});
