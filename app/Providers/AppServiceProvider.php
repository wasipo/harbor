<?php

namespace App\Providers;

use App\Adapter\Shared\LaravelLogger;
use App\Domain\AccessControl\Category\UserCategoryRepositoryInterface;
use App\Domain\AccessControl\Permission\PermissionRepositoryInterface;
use App\Domain\AccessControl\Role\RoleRepositoryInterface;
use App\Domain\Identity\UserRepositoryInterface;
use App\Domain\Shared\Contracts\LoggerInterface;
use App\Infrastructure\AccessControl\Category\UserCategoryRepository;
use App\Infrastructure\AccessControl\Permission\PermissionRepository;
use App\Infrastructure\AccessControl\Role\RoleRepository;
use App\Infrastructure\Identity\UserRepository;
use App\Presenter\Identity\AuthResponseBuilderInterface;
use App\Presenter\Identity\JsonAuthResponseBuilder;
use App\Presenter\Web\Identity\InertiaAuthResponseBuilder;
use App\Presenter\Web\Identity\WebAuthResponseBuilderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );

        $this->app->bind(
            UserCategoryRepositoryInterface::class,
            UserCategoryRepository::class
        );

        // Presenter bindings
        $this->app->bind(
            AuthResponseBuilderInterface::class,
            JsonAuthResponseBuilder::class
        );

        $this->app->bind(
            WebAuthResponseBuilderInterface::class,
            InertiaAuthResponseBuilder::class
        );

        // Logger bindings
        $this->app->bind(
            LoggerInterface::class,
            LaravelLogger::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
