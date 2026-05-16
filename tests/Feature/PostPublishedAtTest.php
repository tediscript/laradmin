<?php

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('publishing a draft auto-sets published_at', function () {
    $post = Post::factory()->unpublished()->create(['user_id' => $this->user->id]);

    expect($post->published_at)->toBeNull();

    $post->update(['published' => true]);

    expect($post->fresh()->published_at)->not->toBeNull();
    expect($post->fresh()->published_at)->toBeInstanceOf(Carbon::class);
});

test('unpublishing does not alter published_at', function () {
    $originalDate = now()->subDays(3);
    $post = Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => $originalDate]);

    $post->update(['published' => false]);

    expect($post->fresh()->published)->toBeFalse();
    expect($post->fresh()->published_at->toDateTimeString())->toBe($originalDate->toDateTimeString());
});

test('manually setting published_at is honored on publish', function () {
    $backdate = now()->subDays(10);
    $post = Post::factory()->unpublished()->create(['user_id' => $this->user->id]);

    $post->update([
        'published' => true,
        'published_at' => $backdate,
    ]);

    expect($post->fresh()->published)->toBeTrue();
    expect($post->fresh()->published_at->toDateTimeString())->toBe($backdate->toDateTimeString());
});

test('creating a published post without published_at auto-sets it', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id, 'published' => true]);

    expect($post->fresh()->published_at)->not->toBeNull();
});

test('re-publishing preserves original published_at', function () {
    $originalDate = now()->subDays(5);
    $post = Post::factory()->published()->create(['user_id' => $this->user->id, 'published_at' => $originalDate]);

    $post->update(['published' => false]);

    $post->update(['published' => true]);

    expect($post->fresh()->published)->toBeTrue();
    expect($post->fresh()->published_at->toDateTimeString())->toBe($originalDate->toDateTimeString());
});
