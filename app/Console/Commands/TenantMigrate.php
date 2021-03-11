<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TenantManager;
use App\Tenant;

class TenantMigrate extends Command {

    protected $signature = 'tenants:migrate';
    
    protected $description = 'Migrate tenant databases';
    
    protected $tenantManager;
    
    protected $migrator;
    
    public function __construct(TenantManager $tenantManager) {
        parent::__construct();

        $this->tenantManager = $tenantManager;
        $this->migrator = app('migrator');
    }
    
    public function handle() {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->tenantManager->setTenant($tenant);
            \DB::connection('tenant')->reconnect();
            $this->migrate();
        }
    }

    private function migrate() {
        $this->prepareDatabase();
        $this->migrator->run(database_path('migrations/defaults'), []);

        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
    }

    protected function prepareDatabase() {
        $this->migrator->setConnection('tenant');

        if (! $this->migrator->repositoryExists()) {
            $this->call('migrate:install');
        }
    }
}