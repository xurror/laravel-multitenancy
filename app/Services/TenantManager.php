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
    
    public function loadTenant($slug) {
        if ($slug !== config('app.domain'))
        {
            if ($slug)
            {
                $tenant = Tenant::where('slug', '=', $slug)->first();

                if ($tenant == null)
                {
                    $tenant = new Tenant();
                    $tenant->slug = $slug;
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
 