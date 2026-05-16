<?php

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PermissionSeeder;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
});

test('create form has published_at input', function () {
    $response = $this->actingAs($this->user)->get('/admin/posts/create');

    $response->assertOk();
    $response->assertSee('name="published_at"', false);
});

test('edit form has published_at input with existing value', function () {
    $date = now()->subDays(2);
    $post = Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => $date]);

    $response = $this->actingAs($this->user)->get("/admin/posts/{$post->id}/edit");

    $response->assertOk();
    $response->assertSee('name="published_at"', false);
});

test('index displays published_at as sortable column', function () {
    Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => now()->subDay()]);
    Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => now()]);

    $response = $this->actingAs($this->user)->get('/admin/posts?sort_by=published_at&sort_direction=asc');

    $response->assertOk();
    $response->assertSee('Published At');
    $response->assertSee('published_at');
});

test('show displays formatted published_at when present', function () {
    $date = now()->subDays(3);
    $post = Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => $date]);

    $response = $this->actingAs($this->user)->get("/admin/posts/{$post->id}");

    $response->assertOk();
    $response->assertSee($date->format('M j, Y'));
});

test('show displays em dash when published_at is null', function () {
    $post = Post::factory()->unpublished()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->get("/admin/posts/{$post->id}");

    $response->assertOk();
    $response->assertSee('—');
});

test('published_at can be stored via form', function () {
    $date = '2026-01-15 10:30';

    $response = $this->actingAs($this->user)->post('/admin/posts', [
        'title' => 'Backdated Post',
        'body' => 'Some content',
        'published' => true,
        'published_at' => $date,
    ]);

    $response->assertRedirect('/admin/posts');

    $post = Post::first();
    expect($post->published_at)->not->toBeNull();
    expect($post->published_at->format('Y-m-d H:i'))->toBe($date);
});

test('published_at can be updated via form', function () {
    $originalDate = now()->subDays(5);
    $post = Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => $originalDate]);
    $newDate = '2026-03-20 14:00';

    $response = $this->actingAs($this->user)->put("/admin/posts/{$post->id}", [
        'title' => $post->title,
        'body' => $post->body,
        'published' => true,
        'published_at' => $newDate,
    ]);

    $response->assertRedirect('/admin/posts');

    $post->refresh();
    expect($post->published_at->format('Y-m-d H:i'))->toBe($newDate);
});

test('posts can be sorted by published_at', function () {
    $oldest = Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => now()->subDays(2)]);
    $newest = Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => now()]);

    $response = $this->actingAs($this->user)->get('/admin/posts?sort_by=published_at&sort_direction=asc');

    $response->assertOk();
    $response->assertSeeInOrder([$oldest->title, $newest->title]);
});
