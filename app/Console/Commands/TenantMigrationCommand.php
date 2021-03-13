<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantMigrationCommand extends Command {

    protected $signature = 'tenancy:migrate
                                {--m|main : Whether we are migrating the main db}
                                {--tenant=* : The tenant database to migrate. \n\t Takes multiple values. \n\t set [all] to migrate all tenants in main db}';

    protected $description = 'Migrate main\tenants databases';

    public function __construct() {
        parent::__construct();
        $this->migrator = app('migrator');
    }

    public function handle() {
        $tenant_opt = $this->option('tenant');
        $main_opt = $this->option('main');

        if ($main_opt) {
            $this->migrate(true);
        }

        if (!empty($tenant_opt)) {
            foreach ($tenant_opt as $opt) {
                if ($opt === "all") {
                    $tenants = Tenant::all();

                    foreach ($tenants as $tenant) {
                        $this->tenant = $tenant;
                        $this->migrate(false);
                    }
                    break;
                }
                else {
                    $this->tenant = $opt;
                    $this->migrate(false);
                }
            }
        }
    }

    private function migrate(bool $is_main) {
        $this->migrator->setConnection($is_main ? 'main' : 'tenant');

        if (! $this->migrator->repositoryExists()) {
            $this->call('migrate:install');
        }

        $this->migrator->run(database_path('migrations/' . $is_main ? 'main' : 'defaults'), []);
    }
}
