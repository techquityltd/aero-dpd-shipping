<?php

namespace DummyNamespace\Providers;

use Aero\Common\Providers\ModuleServiceProvider;
use Illuminate\Routing\Router;

class ServiceProvider extends ModuleServiceProvider
{
    public function register()
    {
        // Autoload the config without needing to publish - remove if not needed.
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php', 'module-name'
        );
    }

    public function boot()
    {
        parent::boot();
        
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'module-name');

        $this->loadRoutes();

        $this->publishAssets('module-name');
        $this->publishViews('module-name');
        $this->publishConfig('module-name');
        $this->publishMigrations('module-name');
    }

    private function publishAssets(string $name)
    {
        $this->publishes([
            __DIR__ . '/../../resources/css' => public_path("vendor/{$name}/css"),
        ], $name);

        $this->publishes([
            __DIR__ . '/../../resources/js' => public_path("vendor/{$name}/js"),
        ], $name);
    }

    private function publishViews(string $name)
    {
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path("views/vendor/{$name}"),
        ], $name);
    }

    private function publishMigrations(string $name)
    {
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => base_path('/database/migrations'),
        ], $name);
    }

    private function publishConfig(string $name)
    {
        $this->publishes([
            __DIR__ . "/../../config/config.php" => base_path("config/{$name}.php"),
        ], $name);
    }

    private function loadRoutes()
    {
        Router::addStoreRoutes(__DIR__ . '/../../routes/store.php');
        Router::addAdminRoutes(__DIR__ . '/../../routes/admin.php');
    }
}
