<?php
namespace App\Services;

use App\Tenant;
use App\Jobs\SetupTenantDatabase;

class TenantManager {
    /*
     * @var null|App\Tenant
     */
     private $tenant;
   
    public function setTenant(?Tenant $tenant) {
        $this->tenant = $tenant;
        return $this;
    }
    
    public function getTenant(): ?Tenant {
        return $this->tenant;
    }
    
    public function loadTenant($identifier) {
        if ($identifier !== config('app.domain'))
        {
            if ($identifier)
            {
                $tenant = Tenant::where('slug', '=', $identifier)->first();

                if ($tenant == null)
                {
                    $tenant = new Tenant();
                    $tenant->slug = $identifier;
                    // error_log($tenant);
                    $tenant->save();
                
                    // Create tenant database and carry out migrations
                    dispatch(new SetupTenantDatabase($tenant, app(TenantManager::class)));
                }
                
                $this->setTenant($tenant);
            }
        }
    }
 }
 