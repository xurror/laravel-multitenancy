<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TenantController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
        $users = User::all();
        return view('welcome');
        // return view('welcome', [
        //     'slug' => $manager->getTenant()->slug,
        //     'users' => $users
        // ]);
    }
}
