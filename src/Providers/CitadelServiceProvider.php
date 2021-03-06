<?php

namespace Cratespace\Citadel\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Cratespace\Citadel\Citadel\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Citadel\Console\InstallCommand;
use Cratespace\Citadel\Actions\ConfirmPassword;
use Cratespace\Citadel\Console\MakeResponseCommand;
use Cratespace\Citadel\Contracts\Actions\ConfirmsPasswords;
use Cratespace\Citadel\Contracts\Providers\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/citadel.php', 'citadel');

        $this->registerAuthGuard();
        $this->registerTwoFactorAuthProvider();
        $this->registerInternalActions();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
        $this->configureRoutes();
        $this->configureCommands();
    }

    /**
     * Register default authentication guard implementation.
     *
     * @return void
     */
    protected function registerAuthGuard(): void
    {
        $this->app->bind(
            StatefulGuard::class,
            fn () => Auth::guard(Config::guard(['null']))
        );
    }

    /**
     * Register two factor authentication provider.
     *
     * @return void
     */
    protected function registerTwoFactorAuthProvider(): void
    {
        $this->app->singleton(
            TwoFactorAuthenticationProviderContract::class,
            TwoFactorAuthenticationProvider::class
        );
    }

    /**
     * Register all citadel internal action classes.
     *
     * @return void
     */
    protected function registerInternalActions(): void
    {
        $this->app->singleton(ConfirmsPasswords::class, ConfirmPassword::class);
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../stubs/config/citadel.php' => config_path('citadel.php'),
            ], 'citadel-config');

            $this->publishes([
                __DIR__ . '/../../stubs/config/rules.php' => config_path('rules.php'),
            ], 'rules-config');

            $this->publishes([
                __DIR__ . '/../../stubs/app/Actions/Citadel/AuthenticateUser.php' => app_path('Actions/Citadel/AuthenticateUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Citadel/CreateNewUser.php' => app_path('Actions/Citadel/CreateNewUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Citadel/DeleteUser.php' => app_path('Actions/Citadel/DeleteUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Citadel/ResetUserPassword.php' => app_path('Actions/Citadel/ResetUserPassword.php'),
                __DIR__ . '/../../stubs/app/Actions/Citadel/UpdateUserPassword.php' => app_path('Actions/Citadel/UpdateUserPassword.php'),
                __DIR__ . '/../../stubs/app/Actions/Citadel/UpdateUserProfile.php' => app_path('Actions/Citadel/UpdateUserProfile.php'),
                __DIR__ . '/../../stubs/app/Actions/Citadel/Traits/PasswordUpdater.php' => app_path('Actions/Citadel/Traits/PasswordUpdater.php'),
                __DIR__ . '/../../stubs/app/Providers/CitadelServiceProvider.php' => app_path('Providers/CitadelServiceProvider.php'),
                __DIR__ . '/../../stubs/app/Providers/AuthServiceProvider.php' => app_path('Providers/AuthServiceProvider.php'),
                __DIR__ . '/../../stubs/app/Policies/UserPolicy.php' => app_path('Policies/UserPolicy.php'),
                __DIR__ . '/../../stubs/app/Models/User.php' => app_path('Models/User.php'),
            ], 'citadel-support');

            $this->publishes([
                __DIR__ . '/../../database/migrations/2014_10_12_000000_create_users_table.php' => database_path('migrations/2014_10_12_000000_create_users_table.php'),
            ], 'citadel-migrations');
        }
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
            MakeResponseCommand::class,
        ]);
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes(): void
    {
        Route::group([
            'namespace' => 'Citadel\Http\Controllers',
            'domain' => Config::domain([null]),
            'prefix' => Config::prefix(),
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');
        });
    }
}
