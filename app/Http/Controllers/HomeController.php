<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;

class HomeController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function checkUserAuthentication(Request $request)
    {
        $userData = [
            'user' => $request->user(),
            'authenticated' => true
        ];

        return $this->sendResponse($userData, 'authenticated');
    }
}
