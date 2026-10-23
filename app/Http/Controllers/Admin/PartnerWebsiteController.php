<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PartnerWebsite;

class PartnerWebsiteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    // ✅ Index (only blade you have)
    public function index()
    {
        $websites = PartnerWebsite::orderBy('display_order')->get();
        return view('admin.partner_websites.index', compact('websites'));
    }

    // ✅ Store (Create via AJAX)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp',
            'url'  => 'required|url'
        ]);

        $logoPath = $request->file('logo')->store('wrbsite_logo', 'public');

        $website = PartnerWebsite::create([
            'name' => $request->name,
            'logo' => $logoPath,
            'url' => $request->url,
            'is_active' => $request->is_active ? 1 : 0,
            'display_order' => $request->display_order ?? 0,
        ]);

        return response()->json([
            'status' => true,
            'data' => $website,
            'message' => 'Website created successfully'
        ]);
    }

    // ✅ Show single (for Edit popup)
    public function show($id)
    {
        $website = PartnerWebsite::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $website
        ]);
    }

    // ✅ Update (Edit via AJAX)
    public function update(Request $request, $id)
    {
        $website = PartnerWebsite::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'url'  => 'required|url'
        ]);

        // update logo if exists
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('wrbsite_logo', 'public');
            $website->logo = $logoPath;
        }

        $website->update([
            'name' => $request->name,
            'url' => $request->url,
            'is_active' => $request->is_active ? 1 : 0,
            'display_order' => $request->display_order ?? 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Website updated successfully'
        ]);
    }

    // ✅ Delete
    public function destroy($id)
    {
        PartnerWebsite::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully'
        ]);
    }

    // ✅ Toggle Active
    public function toggle($id)
    {
        $website = PartnerWebsite::findOrFail($id);
        $website->is_active = !$website->is_active;
        $website->save();

        return response()->json([
            'status' => true,
            'message' => 'Status updated'
        ]);
    }
}