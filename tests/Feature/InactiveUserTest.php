<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows an active user to log in', function (): void {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->postJson('/api/v1/auth/login', [
        'identifier' => $user->email,
        'password' => 'secret123',
    ])->assertOk()->assertJsonStructure(['token', 'user']);
});

it('blocks an inactive user from logging in', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
        'is_active' => false,
    ]);

    $this->postJson('/api/v1/auth/login', [
        'identifier' => $user->email,
        'password' => 'secret123',
    ])->assertUnprocessable()->assertJsonValidationErrors('identifier');
});

it('returns lang and is_active in the user payload', function (): void {
    $user = User::factory()->create(['lang' => 'ar', 'is_active' => true]);

    $this->postJson('/api/v1/auth/login', [
        'identifier' => $user->email,
        'password' => 'password',
    ])->assertOk()
        ->assertJsonPath('user.lang', 'ar')
        ->assertJsonPath('user.is_active', true);
});
