<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\OIDC\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('oidc', Provider::class);
        });

        Blade::directive('datetime', function (string $expression): string {
            return "<?php echo e(
                \\Carbon\\Carbon::instance($expression)
                    ->setTimezone(auth()->user()?->displayTimezone() ?? config('app.timezone'))
                    ->format('d M Y H:i')
            ); ?>";
        });
    }
}
