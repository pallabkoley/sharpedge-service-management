<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Show coupon listing page (single blade)
     */
    public function index()
    {
        $coupons  = Coupon::with('services')->orderByDesc('id')->get();
        $services = Service::active()->get();

        return view('admin.coupons.index', compact('coupons', 'services'));
    } 

    /**
     * Store new coupon (called from Add Modal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:percentage,flat',
            'value' => 'required|numeric',
            'min_amount' => 'nullable|numeric',
            'max_discount' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'max_usage' => 'nullable|integer',
            'max_usage_per_client' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $coupon = Coupon::create([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_amount' => $request->min_amount,
            'max_discount' => $request->max_discount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_usage' => $request->max_usage,
            'max_usage_per_client' => $request->max_usage_per_client,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        if ($request->services) {
            $coupon->services()->sync($request->services);
        }

        return redirect()->back()->with('success', 'Coupon created successfully');
    }

    /**
     * Update coupon (called from Edit Modal)
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,flat',
            'value' => 'required|numeric',
            'min_amount' => 'nullable|numeric',
            'max_discount' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'max_usage' => 'nullable|integer',
            'max_usage_per_client' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $coupon->update([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_amount' => $request->min_amount,
            'max_discount' => $request->max_discount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_usage' => $request->max_usage,
            'max_usage_per_client' => $request->max_usage_per_client,
            'status' => $request->status,
        ]);

        if ($request->services) {
            $coupon->services()->sync($request->services);
        } else {
            $coupon->services()->detach();
        }

        return redirect()->back()->with('success', 'Coupon updated successfully');
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus($id)
    {
        $coupon = Coupon::findOrFail($id);

        $coupon->status = $coupon->status === 'active' ? 'inactive' : 'active';
        $coupon->save();

        return response()->json([
            'success' => true,
            'status' => $coupon->status,
            'message' => 'Coupon status updated successfully'
        ]);
    }
    
    
    
    public function edit($id)
{
    $coupon = Coupon::with('services')->findOrFail($id);

    return response()->json([
        'coupon' => $coupon,
        'services' => $coupon->services->pluck('id'),
    ]);
}


    /**
     * Delete coupon
     */
public function destroy($id)
{
   $coupon = Coupon::findOrFail($id);

   // delete related usage records
   $coupon->usages()->delete();

   // detach services pivot
   $coupon->services()->detach();

   // delete coupon
   $coupon->delete();

   return redirect()->back()->with('success', 'Coupon deleted successfully');
}

}
