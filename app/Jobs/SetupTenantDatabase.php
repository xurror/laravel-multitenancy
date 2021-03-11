<?php

namespace App\Jobs;

use App\Services\TenantManager;
use App\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SetupTenantDatabase implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable;

    protected $tenant;

    protected $tenantManager;

    public function __construct(Tenant $tenant, TenantManager $tenantManager) {
        $this->tenant        = $tenant;
        $this->tenantManager = $tenantManager;
    }

    public function handle() {
        $database    = 'dr_' . $this->tenant->slug;
        config(['database.connections.tenant.database' => $database]);
        
        $connection  = \DB::connection('mysql');
        $createMysql = $connection->statement('CREATE DATABASE ' . $database);


        if ($createMysql) {
            $this->tenantManager->setTenant($this->tenant);
            $connection->reconnect();
            $this->migrate();
        } else {
            $connection->statement('DROP DATABASE ' . $database);
        }
    }

    private function migrate() {
        $migrator = app('migrator');
        $migrator->setConnection('tenant');

        if (! $migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
        }

        $migrator->run(database_path('migrations/defaults'), []);
    }
}
