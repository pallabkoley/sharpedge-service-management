<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\PartnerWebsite;

class PartnerWebsiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // backoffice users
    }

    // ✅ Only display page (no CRUD here)
    public function index()
    {
        $websites = PartnerWebsite::active()
                        ->orderBy('display_order')
                        ->get();

        return view('Backoffice.partner_websites.index', compact('websites'));
    }
}