<?php
namespace App\Services;

use App\Models\Tenant;
use App\Jobs\SetupTenantDatabaseJob;

class SlugService {
    /*
     * @var null|App\Tenant
     */
    private $slug;

    public function __construct()
    {
        $slug = null;
    }

    public function setSlug(?string $slug) {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(): ?string {
        return $this->slug;
    }

 }
