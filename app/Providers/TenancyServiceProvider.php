<?php

namespace App\Providers;

use Illuminate\Http\Request;
use App\Services\SlugService;
use Illuminate\Support\ServiceProvider;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $slugService = new SlugService;

        $this->app->instance(SlugService::class, $slugService);

        $request = $this->app->make(Request::class);
        $slug = explode('.', $request->getHost())[0];
        $slug !== config('app.domain') ? $slugService->setSlug($slug) : $slug = null;

        if (!is_null($slug))
        {
            config(['database.connections.tenant.database' => 'dr_' . $slug]);
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
        $slugService = new SlugService;

        $this->app->instance(TenantManager::class, $slugService);
        $this->app->bind(Tenant::class, function () use ($slugService) {
            return $slugService->getSlug();
        });
    }
}
