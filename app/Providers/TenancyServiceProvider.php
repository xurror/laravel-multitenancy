<?php

namespace App\Providers;

use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $manager = new TenantManager;

        $this->app->instance(TenantManager::class, $manager);

        $request = $this->app->make(Request::class);
        $subdomain = explode('.', $request->getHost())[0];
        $manager->loadTenant($subdomain);

        if (!is_null($manager->getTenant()))
        {
            config(['database.connections.tenant.database' => 'dr_' . $manager->getTenant()->slug]);
            $this->app['db']->setDefaultConnection('tenant');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $manager = new TenantManager;

        $this->app->instance(TenantManager::class, $manager);
        $this->app->bind(Tenant::class, function () use ($manager) {
            return $manager->getTenant();
        });
    }
}
