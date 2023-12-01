<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserToken;
use App\Mail\NewTokenLink;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function __construct()
    {
    }

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): JsonResponse
    {
        $user = $this->getUserFromRequest($request);

        $this->sendToken($user);

        return response()->json([
            'message' => 'A mail was sent to ' . $user->email,
        ]);
    }

    public function login(string $token): RedirectResponse
    {
        $userToken = UserToken::whereToken($token)->first();

        if (! $userToken) {
            abort(403, __('auth.failed'));
        }

        if ($userToken->expires_at < Carbon::now()) {
            abort(403, __('auth.expired'));
        }

        $this->loginWithToken($userToken);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(RouteServiceProvider::HOME);
    }

    protected function getUserFromRequest(LoginRequest $request): User
    {
        if ($request->boolean('new')) {
            return User::query()
                ->create([
                    'email' => $request->input('email'),
                    'name' => $request->input('name'),
                ]);
        }

        return User::query()
            ->where('email', $request->input('email'))
            ->firstOrFail();
    }

    protected function sendToken(User $user): void
    {
        $token = UserToken::query()->make([
            'expires_at' => Carbon::now()->addWeek(),
            'token' => bin2hex(openssl_random_pseudo_bytes(20)),
        ]);

        $user->tokens()->save($token);

        Mail::to($user)->send(new NewTokenLink($token->refresh(), $user->wasRecentlyCreated));
    }

    protected function loginWithToken(UserToken $userToken): void
    {
        $userToken->update([
            'last_used_at' => Carbon::now(),
        ]);

        if (! $userToken->user->email_verified_at) {
            $userToken->user->update([
                'email_verified_at' => Carbon::now(),
            ]);
        }

        Auth::login($userToken->user, true);

        request()->session()->regenerate();
    }
}
