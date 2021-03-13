<?php
namespace App\Services;

use App\Models\Tenant;
use App\Jobs\SetupTenantDatabaseJob;

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
        if (!is_null($slug) && ($slug !== config('app.domain')))
        {
            $tenant = Tenant::where('slug', '=', $slug)->first();

            if ($tenant == null)
            {
                $tenant = new Tenant();
                $tenant->slug = $slug;
                $tenant->save();

                // Create tenant database and carry out migrations
                dispatch(new SetupTenantDatabaseJob($tenant, app(TenantManager::class)));
            }

            $this->setTenant($tenant);
        }

    }
 }
