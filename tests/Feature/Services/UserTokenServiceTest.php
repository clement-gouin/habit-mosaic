<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Services\UserTokenService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTokenServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected UserTokenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[UserTokenService::class];
    }

    /** @test */
    public function it_sends_new_token_to_new_user(): void
    {
        $this->markTestSkipped('TODO');

    }

    /** @test */
    public function it_sends_new_token_to_existing_user(): void
    {
        $this->markTestSkipped('TODO');

    }

    /** @test */
    public function it_sends_new_token_and_invalidates_old_tokens(): void
    {
        $this->markTestSkipped('TODO');

    }

    /** @test */
    public function it_cannot_consume_invalid_token(): void
    {
        $this->markTestSkipped('TODO');

    }

    /** @test */
    public function it_cannot_consume_expired_token(): void
    {
        $this->markTestSkipped('TODO');

    }

    /** @test */
    public function it_consume_token_for_existing_user(): void
    {
        $this->markTestSkipped('TODO');

    }

    /** @test */
    public function it_consume_token_for_new_user(): void
    {
        $this->markTestSkipped('TODO');

    }
}
