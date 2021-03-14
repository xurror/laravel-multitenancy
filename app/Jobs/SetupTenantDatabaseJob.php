<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SetupTenantDatabaseJob implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable;

    protected $tenant;

    public function __construct(Tenant $tenant) {
        $this->tenant        = $tenant;
    }

    public function handle() {
        $database = 'dr_' . $this->tenant->slug;
        config(['database.connections.tenant.database' => $database]);

        $connection  = DB::connection('main');
        $is_created = $connection->statement('CREATE DATABASE ' . $database);

        if ($is_created) {
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
