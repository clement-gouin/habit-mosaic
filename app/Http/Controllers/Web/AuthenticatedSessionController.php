<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\UserTokenService;
use Illuminate\Contracts\View\View;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;

class AuthenticatedSessionController extends Controller
{
    public function __construct(protected UserTokenService $userTokenService)
    {
    }

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): JsonResponse
    {
        $user = $request->toUser();

        $this->userTokenService->sendNewToken($user);

        return response()->json([
            'message' => 'An email was sent to ' . $user->email,
        ]);
    }

    public function login(Request $request, string $token): RedirectResponse
    {
        $user = $this->userTokenService->consumeToken($token);

        if (! $user) {
            abort(403, __('auth.token'));
        }

        Auth::login($user, true);

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(RouteServiceProvider::HOME);
    }
}
