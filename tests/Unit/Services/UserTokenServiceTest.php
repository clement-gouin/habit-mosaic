<?php

namespace Tests\Unit\Services;

use App\Mail\NewTokenLink;
use App\Models\User;
use App\Models\UserToken;
use App\Services\UserTokenService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserTokenServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected UserTokenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new UserTokenService();
    }

    /** @test */
    public function it_sends_new_token_to_user(): void
    {
        $user = User::factory()->create();

        $this->service->sendNewToken($user);

        $this->assertDatabaseHas('user_tokens', [
            'user_id' => $user->id,
            'expires_at' => Carbon::now()->addHour(),
        ]);

        Mail::assertSent(NewTokenLink::class);
    }

    /** @test */
    public function it_sends_new_token_and_invalidates_old_tokens(): void
    {
        $user = User::factory()->create();

        $token = new UserToken([
            'expires_at' => fake()->dateTimeBetween('now', '+1 hour'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $this->service->sendNewToken($user);

        $this->assertDatabaseHas('user_tokens', [
            'id' => $token->id,
            'expires_at' => Carbon::now(),
        ]);
    }

    /** @test */
    public function it_cannot_consume_invalid_token(): void
    {
        $this->assertNull($this->service->consumeToken(fake()->md5));
    }

    /** @test */
    public function it_cannot_consume_expired_token(): void
    {
        $user = User::factory()->create();

        $token = new UserToken([
            'expires_at' => fake()->dateTimeBetween('-1 day', '-1 second'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $this->assertNull($this->service->consumeToken($token->token));
    }

    /** @test */
    public function it_consume_token_for_existing_user(): void
    {
        $user = User::factory()->create();

        $token = new UserToken([
            'expires_at' => fake()->dateTimeBetween('now', '+1 hour'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $result = $this->service->consumeToken($token->token);

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result?->id);

        $this->assertDatabaseHas('user_tokens', [
            'id' => $token->id,
            'expires_at' => Carbon::now(),
        ]);

        $this->assertDatabaseMissing('trackers', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_consume_token_for_new_user(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $token = new UserToken([
            'expires_at' => fake()->dateTimeBetween('now', '+1 hour'),
            'token' => fake()->md5(),
        ]);

        $user->tokens()->save($token);

        $result = $this->service->consumeToken($token->token);

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result?->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
        ]);
    }
}
