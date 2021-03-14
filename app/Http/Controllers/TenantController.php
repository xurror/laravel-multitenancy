<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Services\SlugService;
use App\Jobs\SetupTenantDatabaseJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TenantController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $slugService;

    public function __construct(SlugService $slugService)
    {
        $this->slugService = $slugService;
    }

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
        $users = null;
        $slug = $this->slugService->getSlug();
        $tenant = Tenant::where('slug', '=', $slug)->first();

        if (is_null($slug))
        {
            return view('welcome');
        }
        elseif (!is_null($slug) && is_null($tenant))
        {
            return view('welcome', ['notfound' => true]);
        }
        else
        {
            $users = User::all();
            return view('welcome', ['slug' => $slug, 'users' => $users]);
        }

    }

    /**
     * Show the profile for the given user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'slug' => 'required',
        ]);

        $slug = $request->slug;

        $tenant = Tenant::where('slug', '=', $slug)->first();

        if ($tenant == null)
        {
            $tenant = new Tenant();
            $tenant->slug = $slug;
            $tenant->name = $request->name;
            $tenant->email = $request->email;
            $tenant->save();

            dispatch(new SetupTenantDatabaseJob($tenant));
        }
        return redirect('/');
    }
}
