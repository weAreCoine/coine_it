<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('fails when password is less than 8 characters', function () {
    $this->artisan('users:reset-password', [
        'email' => 'test@example.com',
        'password' => 'short',
    ])->assertFailed();
});

test('fails when user is not found', function () {
    $this->artisan('users:reset-password', [
        'email' => 'nonexistent@example.com',
        'password' => 'validpassword123',
    ])->assertFailed();
});

test('fails when confirmation is denied', function () {
    $user = User::factory()->create(['email' => 'deny@example.com']);

    $this->artisan('users:reset-password', [
        'email' => 'deny@example.com',
        'password' => 'validpassword123',
    ])
        ->expectsConfirmation(
            'Are you sure you want to reset password for user with email: deny@example.com?',
            'no'
        )
        ->assertFailed();
});

test('fails when password confirmation does not match', function () {
    $user = User::factory()->create(['email' => 'mismatch@example.com']);

    $this->artisan('users:reset-password', [
        'email' => 'mismatch@example.com',
        'password' => 'validpassword123',
    ])
        ->expectsConfirmation(
            'Are you sure you want to reset password for user with email: mismatch@example.com?',
            'yes'
        )
        ->expectsQuestion('Confirm new password', 'differentpassword')
        ->assertFailed();
});

test('succeeds and hashes password correctly', function () {
    $user = User::factory()->create(['email' => 'success@example.com']);

    $this->artisan('users:reset-password', [
        'email' => 'success@example.com',
        'password' => 'newpassword123',
    ])
        ->expectsConfirmation(
            'Are you sure you want to reset password for user with email: success@example.com?',
            'yes'
        )
        ->expectsQuestion('Confirm new password', 'newpassword123')
        ->assertSuccessful();

    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});
