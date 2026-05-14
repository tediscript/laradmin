<?php

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    $this->seed(PermissionSeeder::class);
});

test('user without permissions is forbidden from posts index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/posts');

    $response->assertForbidden();
});

test('user without permissions is forbidden from creating posts', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/posts/create')->assertForbidden();
    $this->actingAs($user)->post('/admin/posts', [
        'title' => 'Unauthorized',
    ])->assertForbidden();
});

test('user without permissions is forbidden from updating posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->get("/admin/posts/{$post->id}/edit")->assertForbidden();
    $this->actingAs($user)->put("/admin/posts/{$post->id}", [
        'title' => 'Hacked',
    ])->assertForbidden();
});

test('user without permissions is forbidden from deleting posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->delete("/admin/posts/{$post->id}")->assertForbidden();
});

test('editor can view and create but not delete posts', function () {
    $user = User::factory()->create();
    $user->assignRole('editor');
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    $post = Post::factory()->create(['user_id' => $user->id]);

    // Editor can view
    $this->actingAs($user)->get('/admin/posts')->assertOk();
    $this->actingAs($user)->get("/admin/posts/{$post->id}")->assertOk();

    // Editor can create
    $this->actingAs($user)->get('/admin/posts/create')->assertOk();
    $this->actingAs($user)->post('/admin/posts', [
        'title' => 'Editor Post',
        'body' => 'Content',
    ])->assertRedirect('/admin/posts');

    // Editor can update
    $this->actingAs($user)->get("/admin/posts/{$post->id}/edit")->assertOk();
    $this->actingAs($user)->put("/admin/posts/{$post->id}", [
        'title' => 'Updated by Editor',
    ])->assertRedirect('/admin/posts');

    // Editor cannot delete
    $this->actingAs($user)->delete("/admin/posts/{$post->id}")->assertForbidden();
});

test('admin can perform all post actions', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Admin can view
    $this->actingAs($user)->get('/admin/posts')->assertOk();

    // Admin can create
    $this->actingAs($user)->get('/admin/posts/create')->assertOk();
    $this->actingAs($user)->post('/admin/posts', [
        'title' => 'Admin Post',
    ])->assertRedirect('/admin/posts');

    // Admin can update
    $post = Post::first();
    $this->actingAs($user)->get("/admin/posts/{$post->id}/edit")->assertOk();
    $this->actingAs($user)->put("/admin/posts/{$post->id}", [
        'title' => 'Updated by Admin',
    ])->assertRedirect('/admin/posts');

    // Admin can delete
    $this->actingAs($user)->delete("/admin/posts/{$post->id}")->assertRedirect('/admin/posts');
});
