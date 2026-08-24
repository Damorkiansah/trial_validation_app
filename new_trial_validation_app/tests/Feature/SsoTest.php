<?php

use App\Models\SsoTicket;
use App\Models\User;

test('a valid old-to-new ticket logs the user in', function () {
    $user = User::factory()->create();

    $ticket = SsoTicket::create([
        'token' => str_repeat('a', 64),
        'user_id' => $user->id,
        'direction' => 'old_to_new',
        'expires_at' => now()->addSeconds(30),
    ]);

    $response = $this->get('/sso/exchange?ticket='.$ticket->token);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard'));
});

test('an expired ticket is rejected', function () {
    $user = User::factory()->create();

    $ticket = SsoTicket::create([
        'token' => str_repeat('b', 64),
        'user_id' => $user->id,
        'direction' => 'old_to_new',
        'expires_at' => now()->subSecond(),
    ]);

    $this->get('/sso/exchange?ticket='.$ticket->token);

    $this->assertGuest();
});

test('a ticket cannot be consumed twice', function () {
    $user = User::factory()->create();

    $ticket = SsoTicket::create([
        'token' => str_repeat('c', 64),
        'user_id' => $user->id,
        'direction' => 'old_to_new',
        'expires_at' => now()->addSeconds(30),
    ]);

    $this->get('/sso/exchange?ticket='.$ticket->token);
    $this->post(route('logout'));

    $this->get('/sso/exchange?ticket='.$ticket->token);

    $this->assertGuest();
});

test('an unknown ticket is rejected', function () {
    $this->get('/sso/exchange?ticket=does-not-exist');

    $this->assertGuest();
});
