<?php

namespace Techquity\Dpd\Providers;

use Aero\Common\Providers\ModuleServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Techquity\Dpd\Commands\Install;
use Techquity\Dpd\Drivers\DpdLocalDelivery;

class DpdServiceProvider extends ModuleServiceProvider
{
    public function boot()
    {
        parent::boot();

        Relation::morphMap([
            'dpd_local' => DpdLocalDelivery::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
            ]);
        }

        $this->publishConfig('aero-dpd');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            'aero-dpd'
        );
    }

    private function publishConfig(string $name)
    {
        $this->publishes([
            __DIR__ . "/../../config/config.php" => base_path("config/{$name}.php"),
        ], $name);
    }
}
