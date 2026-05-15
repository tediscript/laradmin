<?php

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PermissionSeeder;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

test('guests cannot access posts', function () {
    $this->get('/admin/posts')->assertRedirect('/login');
    $this->get('/admin/posts/create')->assertRedirect('/login');
    $this->post('/admin/posts')->assertRedirect('/login');
});

test('index displays posts table', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/admin/posts');

    $response->assertOk();
    $response->assertSee('Posts');
});

test('create displays the form', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/posts/create');

    $response->assertOk();
    $response->assertSee('Create Post');
});

test('store creates a new post', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post('/admin/posts', [
        'title' => 'My First Post',
        'body' => 'This is the body.',
        'published' => true,
    ]);

    $response->assertRedirect('/admin/posts');
    $response->assertSessionHas('status', 'Post created successfully.');

    $post = Post::first();
    expect($post)->not->toBeNull();
    expect($post->title)->toBe('My First Post');
    expect($post->slug)->toBe('my-first-post');
    expect($post->body)->toBe('This is the body.');
    expect($post->published)->toBeTrue();
    expect($post->user_id)->toBe($user->id);
});

test('store validates required fields', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post('/admin/posts', []);

    $response->assertSessionHasErrors(['title']);
});

test('show displays post detail', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $post = Post::factory()->create(['user_id' => $user->id, 'title' => 'Test Post']);

    $response = $this->actingAs($user)->get("/admin/posts/{$post->id}");

    $response->assertOk();
    $response->assertSee('Test Post');
});

test('edit displays the form with post data', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $post = Post::factory()->create(['user_id' => $user->id, 'title' => 'Edit Me']);

    $response = $this->actingAs($user)->get("/admin/posts/{$post->id}/edit");

    $response->assertOk();
    $response->assertSee('Edit Post');
    $response->assertSee('Edit Me');
});

test('update modifies the post', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put("/admin/posts/{$post->id}", [
        'title' => 'Updated Title',
        'body' => 'Updated body.',
        'published' => false,
    ]);

    $response->assertRedirect('/admin/posts');
    $response->assertSessionHas('status', 'Post updated successfully.');

    $post->refresh();
    expect($post->title)->toBe('Updated Title');
    expect($post->body)->toBe('Updated body.');
    expect($post->published)->toBeFalse();
});

test('destroy soft deletes the post', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("/admin/posts/{$post->id}");

    $response->assertRedirect('/admin/posts');
    $response->assertSessionHas('status', 'Post deleted successfully.');

    expect(Post::find($post->id))->toBeNull();
    expect(Post::withTrashed()->find($post->id))->not->toBeNull();
    expect(Post::withTrashed()->find($post->id)->deleted_at)->not->toBeNull();
});

test('slug is auto-generated from title', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)->post('/admin/posts', [
        'title' => 'Hello World Example',
    ]);

    $post = Post::first();
    expect($post->slug)->toBe('hello-world-example');
});

test('slug handles duplicates', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    Post::factory()->create(['title' => 'Duplicate', 'slug' => 'duplicate', 'user_id' => $user->id]);

    $this->actingAs($user)->post('/admin/posts', [
        'title' => 'Duplicate',
    ]);

    $post = Post::where('slug', '!=', 'duplicate')->first();
    expect($post->slug)->toBe('duplicate-1');
});

test('store defaults published to false when unchecked', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)->post('/admin/posts', [
        'title' => 'Draft Post',
        'published' => '0',
    ]);

    $post = Post::first();
    expect($post->published)->toBeFalse();
});

test('posts can be sorted by title ascending', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Beta Post']);
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Alpha Post']);

    $response = $this->actingAs($user)->get('/admin/posts?sort_by=title&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder(['Alpha Post', 'Beta Post']);
});

test('posts can be sorted by title descending', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Alpha Post']);
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Beta Post']);

    $response = $this->actingAs($user)->get('/admin/posts?sort_by=title&sort_direction=desc');

    $response->assertOk();
    $response->assertSeeInOrder(['Beta Post', 'Alpha Post']);
});

test('posts can be searched by title', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Laravel Tips']);
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Vue Tricks']);

    $response = $this->actingAs($user)->get('/admin/posts?search=Laravel');

    $response->assertOk();
    $response->assertSee('Laravel Tips');
    $response->assertDontSee('Vue Tricks');
});

test('posts can be searched by body', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Post A', 'body' => 'Content about Laravel']);
    Post::factory()->create(['user_id' => $user->id, 'title' => 'Post B', 'body' => 'Content about Vue']);

    $response = $this->actingAs($user)->get('/admin/posts?search=Laravel');

    $response->assertOk();
    $response->assertSee('Post A');
    $response->assertDontSee('Post B');
});

test('posts search shows empty state message', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/posts?search=nonexistent');

    $response->assertOk();
    $response->assertSee('No posts found matching');
});

test('posts index ignores invalid sort parameters', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/admin/posts?sort_by=invalid_column&sort_direction=invalid');

    $response->assertOk();
    $response->assertSee('Posts');
});

test('posts can be sorted by author name ascending', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $authorB = User::factory()->create(['name' => 'Bob Author']);
    $authorA = User::factory()->create(['name' => 'Alice Author']);
    Post::factory()->create(['user_id' => $authorB->id, 'title' => 'Post by Bob']);
    Post::factory()->create(['user_id' => $authorA->id, 'title' => 'Post by Alice']);

    $response = $this->actingAs($admin)->get('/admin/posts?sort_by=user_name&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder(['Post by Alice', 'Post by Bob']);
});

test('posts can be sorted by author name descending', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $authorA = User::factory()->create(['name' => 'Alice Author']);
    $authorB = User::factory()->create(['name' => 'Bob Author']);
    Post::factory()->create(['user_id' => $authorA->id, 'title' => 'Post by Alice']);
    Post::factory()->create(['user_id' => $authorB->id, 'title' => 'Post by Bob']);

    $response = $this->actingAs($admin)->get('/admin/posts?sort_by=user_name&sort_direction=desc');

    $response->assertOk();
    $response->assertSeeInOrder(['Post by Bob', 'Post by Alice']);
});
