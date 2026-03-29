<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRole('viewer');
});

describe('login', function () {
    it('shows the login page', function () {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Sign In');
    });

    it('authenticates a user with valid credentials', function () {
        $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($this->user);
    });

    it('rejects invalid credentials', function () {
        $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('redirects authenticated users away from login page', function () {
        $this->actingAs($this->user)
            ->get(route('login'))
            ->assertRedirect();
    });
});

describe('register', function () {
    it('shows the registration page', function () {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Register');
    });

    it('creates a new user', function () {
        $this->post(route('register'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'SecureP@ss1',
            'password_confirmation' => 'SecureP@ss1',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);

        $newUser = User::where('email', 'newuser@example.com')->first();
        expect($newUser->hasRole('viewer'))->toBeTrue();
        $this->assertAuthenticatedAs($newUser);
    });

    it('rejects registration with duplicate email', function () {
        $this->post(route('register'), [
            'name' => 'Duplicate',
            'email' => $this->user->email,
            'password' => 'SecureP@ss1',
            'password_confirmation' => 'SecureP@ss1',
        ])->assertSessionHasErrors('email');
    });

    it('requires password confirmation', function () {
        $this->post(route('register'), [
            'name' => 'No Confirm',
            'email' => 'noconfirm@example.com',
            'password' => 'SecureP@ss1',
            'password_confirmation' => 'WrongConfirm1',
        ])->assertSessionHasErrors('password');
    });
});

describe('logout', function () {
    it('logs out an authenticated user', function () {
        $this->actingAs($this->user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    });
});

describe('unauthenticated access', function () {
    it('redirects guests to login for dashboard', function () {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    });

    it('redirects guests to login for items', function () {
        $this->get(route('items.index'))
            ->assertRedirect(route('login'));
    });

    it('redirects guests to login for categories', function () {
        $this->get(route('categories.index'))
            ->assertRedirect(route('login'));
    });
});
