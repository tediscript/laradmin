<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

test('login page shows google button', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login with Google');
});

test('redirect route forwards to google', function () {
    $socialiteDriver = Mockery::mock(Provider::class);
    $socialiteDriver->shouldReceive('redirect')->once()->andReturn(
        redirect('https://accounts.google.com/o/oauth2/auth')
    );
    Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

    $response = $this->get('/login/google/redirect');

    $response->assertRedirectContains('google.com');
});

test('redirect rejects unsupported provider', function () {
    $response = $this->get('/login/facebook/redirect');

    $response->assertStatus(404);
});

test('callback creates new user when no matching email', function () {
    $socialiteUser = createSocialiteUser(
        id: '123456789',
        name: 'Jane Doe',
        email: 'jane@example.com',
        avatar: 'https://example.com/avatar.jpg',
        token: 'google-token',
        refreshToken: 'google-refresh-token',
        expiresIn: 3600,
    );

    $socialiteDriver = Mockery::mock(Provider::class);
    $socialiteDriver->shouldReceive('user')->once()->andReturn($socialiteUser);
    Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

    $response = $this->get('/login/google/callback');

    // User created
    $user = User::where('email', 'jane@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Jane Doe');
    expect($user->password)->toBeNull();
    expect($user->email_verified_at)->not->toBeNull();

    // Social account linked
    $socialAccount = SocialAccount::where('provider', 'google')
        ->where('provider_user_id', '123456789')
        ->first();
    expect($socialAccount)->not->toBeNull();
    expect($socialAccount->user_id)->toBe($user->id);
    expect($socialAccount->token)->toBe('google-token');

    // Authenticated
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('callback links google to existing user by email', function () {
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
        'name' => 'Existing User',
    ]);

    $socialiteUser = createSocialiteUser(
        id: '987654321',
        name: 'Existing User',
        email: 'existing@example.com',
        avatar: 'https://example.com/avatar.jpg',
        token: 'google-token-2',
        refreshToken: 'google-refresh-token-2',
        expiresIn: 3600,
    );

    $socialiteDriver = Mockery::mock(Provider::class);
    $socialiteDriver->shouldReceive('user')->once()->andReturn($socialiteUser);
    Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

    $response = $this->get('/login/google/callback');

    // No duplicate user created
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);

    // Social account linked to existing user
    $socialAccount = SocialAccount::where('provider', 'google')
        ->where('provider_user_id', '987654321')
        ->first();
    expect($socialAccount)->not->toBeNull();
    expect($socialAccount->user_id)->toBe($existingUser->id);

    // Authenticated as existing user
    $this->assertAuthenticatedAs($existingUser);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('callback logs in already-linked user directly', function () {
    $user = User::factory()->create([
        'email' => 'linked@example.com',
    ]);
    SocialAccount::factory()->create([
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_user_id' => '111222333',
    ]);

    $socialiteUser = createSocialiteUser(
        id: '111222333',
        name: 'Linked User',
        email: 'linked@example.com',
        avatar: 'https://example.com/avatar.jpg',
        token: 'google-token-3',
        refreshToken: 'google-refresh-token-3',
        expiresIn: 3600,
    );

    $socialiteDriver = Mockery::mock(Provider::class);
    $socialiteDriver->shouldReceive('user')->once()->andReturn($socialiteUser);
    Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

    $response = $this->get('/login/google/callback');

    // No new user or social account created
    expect(User::count())->toBe(1);
    expect(SocialAccount::count())->toBe(1);

    // Token updated on existing social account
    $socialAccount = SocialAccount::first();
    expect($socialAccount->token)->toBe('google-token-3');

    // Authenticated
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

function createSocialiteUser(
    string $id,
    string $name,
    string $email,
    string $avatar,
    string $token,
    string $refreshToken,
    int $expiresIn,
): SocialiteUser {
    $user = new SocialiteUser;
    $user->id = $id;
    $user->name = $name;
    $user->email = $email;
    $user->avatar = $avatar;
    $user->token = $token;
    $user->refreshToken = $refreshToken;
    $user->expiresIn = $expiresIn;

    return $user;
}
