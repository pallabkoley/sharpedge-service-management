<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MarketingClientController extends Controller
{
    /**
     * Client Listing + Filters
     */
    public function index(Request $request)
{
    $status = $request->get('status', 'all');
    $search = $request->get('search');
    $loggedInUserId = Auth::id(); // 🔑 current marketer

    $query = Client::where('created_by', $loggedInUserId); // 🔥 ONLY HIS CLIENTS

    // Status filter
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // Search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }

    $clients = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    // 📊 Dashboard counts (ONLY HIS DATA)
    $totalClients = Client::where('created_by', $loggedInUserId)->count();

    $activeClients = Client::where('created_by', $loggedInUserId)
        ->where('status', 'active')
        ->count();

    $inactiveClients = Client::where('created_by', $loggedInUserId)
        ->where('status', 'inactive')
        ->count();

    $pendingClients = Client::where('created_by', $loggedInUserId)
        ->where('status', 'pending')
        ->count();

    return view('Marketing.client.index', compact(
        'clients',
        'status',
        'totalClients',
        'activeClients',
        'inactiveClients',
        'pendingClients'
    ));
}


    /**
     * Store New Client (USER + CLIENT)
     */
    public function store(Request $request)
    {
        $request->validate([
            // USER (login)
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',

            // CLIENT
            'phone'         => 'required|unique:clients,phone',
            'profile_photo' => 'nullable|image|max:2048',
            'status'        => 'required',
        ]);

        // Get CLIENT role
        $clientRole = Role::where('name', 'client')->firstOrFail();

        // 1️⃣ Create USER
        $user = User::create([
            'role_id' => $clientRole->id,
            'name'    => $request->name,
            'email'   => $request->email,
            'password'=> Hash::make($request->password),
            'phone'   => $request->phone,
            'status'  => 'active',
        ]);

        // 2️⃣ Upload profile photo
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('clients', 'public');
        }

        // 3️⃣ Create CLIENT
        Client::create([
            'user_id'    => $user->id,
            'created_by'=> Auth::id(),

            // basic
            'name'   => $request->name,
            'email'  => $request->email,
            'phone'  => $request->phone,
            'profile_photo' => $photoPath,
            'status' => $request->status,

            // personal
            'dob'    => $request->dob,
            'gender' => $request->gender,

            // address
            'address'=> $request->address,
            'city'   => $request->city,
            'state'  => $request->state,
            'pincode'=> $request->pincode,

            // business
            'company_name' => $request->company_name,
            'gst_number'   => $request->gst_number,

            // communication
            'preferred_contact' => $request->preferred_contact,
            'source'            => $request->source,
            'notes'             => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Client created successfully.');
    }

    /**
     * Edit Client (AJAX)
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return response()->json([
            'success' => true,
            'client'  => $client
        ]);
    }

    /**
     * Update Client (Client profile only)
     * Password is NOT updated here
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $user   = $client->user;

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|unique:clients,phone,' . $client->id,
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        // Update USER
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Handle photo
        $photoPath = $client->profile_photo;
        if ($request->hasFile('profile_photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('profile_photo')->store('clients', 'public');
        }

        // Update CLIENT
        $client->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'phone'  => $request->phone,
            'profile_photo' => $photoPath,
            'status' => $request->status,

            'dob'    => $request->dob,
            'gender' => $request->gender,

            'address'=> $request->address,
            'city'   => $request->city,
            'state'  => $request->state,
            'pincode'=> $request->pincode,

            'company_name' => $request->company_name,
            'gst_number'   => $request->gst_number,

            'preferred_contact' => $request->preferred_contact,
            'source'            => $request->source,
            'notes'             => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Client updated successfully.');
    }

    /**
     * Delete Client (USER + CLIENT)
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $user   = $client->user;

        if ($client->profile_photo) {
            Storage::disk('public')->delete($client->profile_photo);
        }

        $client->delete();
        $user->delete();

        return redirect()->back()->with('success', 'Client deleted successfully.');
    }
    
    public function show($id)
    {
        $client = Client::with('user')->findOrFail($id);
        return view('Marketing.client.show', compact('client'));
    }
}
