<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

test('login page shows OIDC button', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login with OIDC');
});

test('OIDC redirect route forwards to OIDC provider', function () {
    $socialiteDriver = Mockery::mock(Provider::class);
    $socialiteDriver->shouldReceive('redirect')->once()->andReturn(
        redirect('https://auth.example.com/authorize')
    );
    Socialite::shouldReceive('driver')->with('oidc')->andReturn($socialiteDriver);

    $response = $this->get('/login/oidc/redirect');

    $response->assertRedirectContains('auth.example.com');
});

test('OIDC callback creates new user when no matching email', function () {
    $socialiteUser = createOidcSocialiteUser(
        id: 'oidc-user-001',
        name: 'OIDC User',
        email: 'oidc@example.com',
        avatar: 'https://example.com/avatar.jpg',
        token: 'oidc-token',
        refreshToken: 'oidc-refresh-token',
        expiresIn: 3600,
    );

    $socialiteDriver = Mockery::mock(Provider::class);
    $socialiteDriver->shouldReceive('user')->once()->andReturn($socialiteUser);
    Socialite::shouldReceive('driver')->with('oidc')->andReturn($socialiteDriver);

    $response = $this->get('/login/oidc/callback');

    // User created
    $user = User::where('email', 'oidc@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('OIDC User');
    expect($user->password)->toBeNull();
    expect($user->email_verified_at)->not->toBeNull();

    // Social account linked with OIDC provider
    $socialAccount = SocialAccount::where('provider', 'oidc')
        ->where('provider_user_id', 'oidc-user-001')
        ->first();
    expect($socialAccount)->not->toBeNull();
    expect($socialAccount->user_id)->toBe($user->id);
    expect($socialAccount->token)->toBe('oidc-token');

    // Authenticated
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('OIDC callback links to existing user by email', function () {
    $existingUser = User::factory()->create([
        'email' => 'existing-oidc@example.com',
        'name' => 'Existing OIDC User',
    ]);

    $socialiteUser = createOidcSocialiteUser(
        id: 'oidc-user-002',
        name: 'Existing OIDC User',
        email: 'existing-oidc@example.com',
        avatar: 'https://example.com/avatar.jpg',
        token: 'oidc-token-2',
        refreshToken: 'oidc-refresh-token-2',
        expiresIn: 3600,
    );

    $socialiteDriver = Mockery::mock(Provider::class);
    $socialiteDriver->shouldReceive('user')->once()->andReturn($socialiteUser);
    Socialite::shouldReceive('driver')->with('oidc')->andReturn($socialiteDriver);

    $response = $this->get('/login/oidc/callback');

    // No duplicate user created
    expect(User::where('email', 'existing-oidc@example.com')->count())->toBe(1);

    // Social account linked to existing user
    $socialAccount = SocialAccount::where('provider', 'oidc')
        ->where('provider_user_id', 'oidc-user-002')
        ->first();
    expect($socialAccount)->not->toBeNull();
    expect($socialAccount->user_id)->toBe($existingUser->id);

    // Authenticated as existing user
    $this->assertAuthenticatedAs($existingUser);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

function createOidcSocialiteUser(
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
