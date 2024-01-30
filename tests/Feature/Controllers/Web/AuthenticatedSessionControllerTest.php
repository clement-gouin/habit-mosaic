<?php

namespace Tests\Feature\Controllers\Web;

use App\Models\User;
use App\Services\UserTokenService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Auth::logout();
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

        $this->getMock(UserTokenService::class)
            ->expects('sendNewToken')
            ->with(Mockery::on(fn (User $arg) => $arg->email === $data['email']));

        $this->postJson(route('login.store'), [
            'new' => true,
            ...$data,
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', $data);
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

        $this->getMock(UserTokenService::class)
            ->expects('sendNewToken')
            ->with(self::modelArg($user));

        $this->postJson(route('login.store'), [
            'new' => false,
            'email' => $user->email,
        ])->assertSuccessful();
    }

    /** @test */
    public function it_cannot_login_user_with_invalid_token(): void
    {
        $token = fake()->md5();

        $this->getMock(UserTokenService::class)
            ->expects('consumeToken')
            ->with($token)
            ->andReturn(null);

        $this->get(route('login.token', $token))->assertForbidden();
    }

    /** @test */
    public function it_logins_existing_user(): void
    {
        $user = User::factory()->create();

        $token = fake()->md5();

        $this->getMock(UserTokenService::class)
            ->expects('consumeToken')
            ->with($token)
            ->andReturn($user);

        $this->get(route('login.token', $token))->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_logout_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('logout'))->assertRedirect();

        $this->assertGuest();
    }
}
