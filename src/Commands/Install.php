<?php

namespace Techquity\Dpd\Commands;

use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Console\Command;

class Install extends Command
{
    protected $signature = 'tqt:dpd:install';

    public function handle()
    {
        FulfillmentMethod::updateOrCreate(
            ['driver' => 'dpd_local'],
            ['name' => 'DPD Local Domestic Ship to Shop']
        );
    }
}
