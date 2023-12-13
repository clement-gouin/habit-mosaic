<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\UserToken;
use App\Mail\NewTokenLink;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthenticatedSessionControllerTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        Auth::logout();

        $this->freezeTime();
    }

    /** @test */
    public function it_cannot_register_existing_user(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('login.store'), [
            'new' => true,
            'email' => $user->email,
            'name' => $user->name,
        ])->assertUnprocessable();
    }

    /** @test */
    public function it_registers_new_user_and_sends_link(): void
    {
        $data = [
            'email' => fake()->email(),
            'name' => fake()->name(),
        ];

        $this->postJson(route('login.store'), [
            'new' => true,
            ...$data,
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', $data);

        $user = User::whereEmail($data['email'])->first();

        $this->assertDatabaseHas('user_tokens', [
            'user_id' => $user->id,
            'expires_at' => Carbon::now()->addHour(),
        ]);

        Mail::assertSent(NewTokenLink::class);
    }

    /** @test */
    public function it_cannot_login_not_registered_user(): void
    {
        $this->postJson(route('login.store'), [
            'new' => false,
            'email' => fake()->email(),
        ])->assertUnprocessable();
    }

    /** @test */
    public function it_sends_link_to_existing_user(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('login.store'), [
            'new' => false,
            'email' => $user->email,
        ])->assertSuccessful();

        $this->assertDatabaseHas('user_tokens', [
            'user_id' => $user->id,
            'expires_at' => Carbon::now()->addHour(),
        ]);

        Mail::assertSent(NewTokenLink::class);
    }

    /** @test */
    public function it_invalidates_old_tokens(): void
    {
        $user = User::factory()->create();

        $token = UserToken::query()->make([
            'expires_at' => fake()->dateTimeBetween('now', '+1 hour'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $this->postJson(route('login.store'), [
            'new' => false,
            'email' => $user->email,
        ])->assertSuccessful();

        $this->assertDatabaseHas('user_tokens', [
            'id' => $token->id,
            'expires_at' => Carbon::now(),
        ]);
    }

    /** @test */
    public function it_cannot_login_user_with_invalid_token(): void
    {
        $this->get(route('login.token', fake()->md5()))->assertForbidden();
    }

    /** @test */
    public function it_cannot_login_user_with_expired_token(): void
    {
        $user = User::factory()->create();

        $token = UserToken::query()->make([
            'expires_at' => fake()->dateTimeBetween('-1 hour', 'now'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $this->get(route('login.token', $token->token))->assertForbidden();
    }

    /** @test */
    public function it_logins_existing_user(): void
    {
        $user = User::factory()->create();

        $token = UserToken::query()->make([
            'expires_at' => fake()->dateTimeBetween('now', '+1 hour'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $this->get(route('login.token', $token->token))->assertRedirect();

        $this->assertDatabaseHas('user_tokens', [
            'id' => $token->id,
            'expires_at' => Carbon::now(),
        ]);

        $this->assertAuthenticatedAs($user);

        $user = $user->refresh();

        $this->assertEmpty($user->trackers);
        $this->assertEmpty($user->categories);
    }

    /** @test */
    public function it_logins_new_user(): void
    {
        $user = User::factory()->unverified()->create();

        $token = UserToken::query()->make([
            'expires_at' => fake()->dateTimeBetween('now', '+1 hour'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $this->get(route('login.token', $token->token))->assertRedirect();

        $this->assertDatabaseHas('user_tokens', [
            'id' => $token->id,
            'expires_at' => Carbon::now(),
        ]);

        $this->assertAuthenticatedAs($user);

        $user = $user->refresh();

        $this->assertNotNull($user->email_verified_at);

        $this->assertNotEmpty($user->trackers);
        $this->assertNotEmpty($user->categories);
    }

    /** @test */
    public function it_logout_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('logout'))->assertRedirect();

        $this->assertGuest();
    }
}
