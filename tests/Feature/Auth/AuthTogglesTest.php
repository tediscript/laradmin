<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('all auth methods enabled by default', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login with Google');
    $response->assertSee('Login with OIDC');
    $response->assertSee('Log in');
    $response->assertSee('Email');
    $response->assertSee('Password');
});

test('password login can be disabled', function () {
    config(['services.auth.password_login' => false]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertDontSee('Log in');
    $response->assertDontSee('Email');
});

test('registration hidden when password login disabled', function () {
    config(['services.auth.password_login' => false]);

    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('registration visible when password login enabled', function () {
    config(['services.auth.password_login' => true]);

    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('google login can be disabled', function () {
    config(['services.auth.google' => false]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertDontSee('Login with Google');
});

test('google routes return 404 when disabled', function () {
    config(['services.auth.google' => false]);

    $this->get('/login/google/redirect')->assertStatus(404);
    $this->get('/login/google/callback')->assertStatus(404);
});

test('oidc login can be disabled', function () {
    config(['services.auth.oidc' => false]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertDontSee('Login with OIDC');
});

test('oidc routes return 404 when disabled', function () {
    config(['services.auth.oidc' => false]);

    $this->get('/login/oidc/redirect')->assertStatus(404);
    $this->get('/login/oidc/callback')->assertStatus(404);
});

test('lockout mode shows disabled message when all disabled', function () {
    config([
        'services.auth.password_login' => false,
        'services.auth.google' => false,
        'services.auth.oidc' => false,
    ]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login is disabled');
    $response->assertDontSee('Login with Google');
    $response->assertDontSee('Login with OIDC');
    $response->assertDontSee('Log in');
});

test('only password login enabled', function () {
    config([
        'services.auth.password_login' => true,
        'services.auth.google' => false,
        'services.auth.oidc' => false,
    ]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Log in');
    $response->assertDontSee('Login with Google');
    $response->assertDontSee('Login with OIDC');
});

test('only google login enabled', function () {
    config([
        'services.auth.password_login' => false,
        'services.auth.google' => true,
        'services.auth.oidc' => false,
    ]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login with Google');
    $response->assertDontSee('Login with OIDC');
    $response->assertDontSee('Log in');
});

test('only oidc login enabled', function () {
    config([
        'services.auth.password_login' => false,
        'services.auth.google' => false,
        'services.auth.oidc' => true,
    ]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login with OIDC');
    $response->assertDontSee('Login with Google');
    $response->assertDontSee('Log in');
});
