<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * The providers supported for social login.
     *
     * @var array<int, string>
     */
    protected array $providers = ['google', 'oidc'];

    /**
     * Redirect the user to the OAuth provider.
     */
    public function redirect(string $provider)
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        if (! config("services.auth.{$provider}", true)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the OAuth provider.
     */
    public function callback(string $provider)
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        if (! config("services.auth.{$provider}", true)) {
            abort(404);
        }

        $socialiteUser = Socialite::driver($provider)->user();

        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $socialiteUser->getId())
            ->first();

        if ($socialAccount) {
            $socialAccount->update([
                'token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'expires_at' => $socialiteUser->expiresIn ? now()->addSeconds($socialiteUser->expiresIn) : null,
            ]);

            Auth::login($socialAccount->user);

            return redirect()->intended(route('admin.dashboard'));
        }

        return DB::transaction(function () use ($provider, $socialiteUser) {
            $user = User::where('email', $socialiteUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name' => $socialiteUser->getName(),
                    'email' => $socialiteUser->getEmail(),
                    'email_verified_at' => now(),
                ]);
            }

            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_user_id' => $socialiteUser->getId(),
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'avatar' => $socialiteUser->getAvatar(),
                'token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'expires_at' => $socialiteUser->expiresIn ? now()->addSeconds($socialiteUser->expiresIn) : null,
            ]);

            Auth::login($user);

            return redirect()->intended(route('admin.dashboard'));
        });
    }
}
