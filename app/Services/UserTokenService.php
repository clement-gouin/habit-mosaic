<?php

namespace App\Services;

use App\Mail\NewTokenLink;
use App\Models\User;
use App\Models\UserToken;
use Database\Seeders\UserBaseTrackersSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class UserTokenService extends Service
{
    protected const TOKEN_EXPIRES = '1 hour';

    public function sendNewToken(User $user): void
    {
        $user->tokens()
            ->where('expires_at', '>', Carbon::now())
            ->update([
                'expires_at' => Carbon::now(),
            ]);

        $token = new UserToken([
            'expires_at' => Carbon::now()->add(static::TOKEN_EXPIRES),
            'token' => bin2hex(openssl_random_pseudo_bytes(20)),
        ]);

        $user->tokens()->save($token);

        Mail::to($user)->send(new NewTokenLink($token->refresh(), $user->wasRecentlyCreated));
    }

    public function consumeToken(string $token): ?User
    {
        $userToken = UserToken::whereToken($token)->first();

        if (! $userToken || $userToken->expires_at < Carbon::now()) {
            return null;
        }

        $userToken->update([
            'expires_at' => Carbon::now(),
        ]);

        if (! $userToken->user->email_verified_at) {
            (new UserBaseTrackersSeeder())->run($userToken->user);

            $userToken->user->update([
                'email_verified_at' => Carbon::now(),
            ]);
        }

        return $userToken->user;
    }
}
