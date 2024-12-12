<?php

namespace App\Providers;

use App\Models\Passport\Token;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerPassport();
        $this->registerPermissions();

        parent::register();
    }

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }

    private function registerPassport(): void
    {
        Passport::useTokenModel(Token::class);

        Passport::ignoreRoutes();
    }

    private function registerPermissions(): void
    {
        Gate::before(static function (User $user, $ability) {
            return null;
        });

        Gate::after(static function (User $user, $ability) {
            return true;
        });
    }
}
