<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;

class BackofficeHomeController extends Controller
{
    /**
     * Backoffice dashboard page
     */
    public function index()
    {
        return view('Backoffice.Dashboard');
    }
}
