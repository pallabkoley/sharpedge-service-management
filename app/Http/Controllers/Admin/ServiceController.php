<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * =========================
     * LIST SERVICES
     * =========================
     */
    public function index(Request $request)
    {
        $categories = ServiceCategory::active()
            ->orderBy('display_order')
            ->get();

        $totalServices    = Service::count();
        $activeServices   = Service::where('status', 'active')->count();
        $inactiveServices = Service::where('status', 'inactive')->count();

        // AJAX request for listing
        if ($request->expectsJson()) {

            $status      = $request->get('status', 'all');
            $search      = $request->get('search');
            $category_id = $request->get('category_id');

            $services = Service::with([
                    'category',
                    'documentRequirements.documentMaster',
                    'workProcess.steps',
                ])
                ->when($status !== 'all', function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->when($category_id, function ($q) use ($category_id) {
                    $q->where('service_category_id', $category_id);
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($x) use ($search) {
                        $x->where('service_name', 'like', "%{$search}%")
                          ->orWhere('service_code', 'like', "%{$search}%")
                          ->orWhere('govt_fee', 'like', "%{$search}%");
                    });
                })
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success'  => true,
                'services' => $services,
                'total'    => $services->count(),
            ]);
        }

        // Blade load
        return view('admin.services.index', compact(
            'categories',
            'totalServices',
            'activeServices',
            'inactiveServices'
        ));
    }

    /**
     * =========================
     * CREATE SERVICE
     * =========================
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_code'        => 'nullable|string|max:50|unique:services,service_code',
            'service_name'        => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'govt_fee'            => 'nullable|string|max:255',
            'our_fee'             => 'nullable|numeric|min:0',
            'is_taxable'          => 'required|boolean',
            'tax_percentage'      => 'nullable|required_if:is_taxable,1|numeric|min:0',
            'allow_discount'      => 'required|boolean',
            'visible_to_client'   => 'required|boolean',
            'processing_time'     => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'status'              => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $maxOrder = Service::where('service_category_id', $request->service_category_id)
                ->max('order');

            // Generate service code if not provided
            $serviceCode = $request->service_code;
            if (empty($serviceCode)) {
                $serviceCode = $this->generateServiceCode();
            }

            $service = Service::create([
                'service_code'        => $serviceCode,
                'service_name'        => $request->service_name,
                'service_category_id' => $request->service_category_id,
                'govt_fee'            => $request->govt_fee,
                'our_fee'             => $request->our_fee !== '' ? $request->our_fee : null,
                'is_taxable'          => $request->is_taxable,
                'tax_percentage'      => $request->is_taxable ? $request->tax_percentage : null,
                'allow_discount'      => $request->allow_discount,
                'visible_to_client'   => $request->visible_to_client,
                'processing_time'     => $request->processing_time,
                'description'         => $request->description,
                'status'              => $request->status,
                'order'               => $maxOrder ? $maxOrder + 1 : 1,
            ]);

            $this->updateCategoryCount($request->service_category_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'service' => $service->load('category'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
            ], 500);
        }
    }

    /**
     * =========================
     * SHOW SERVICE
     * =========================
     */
    public function show($id)
    {
        $service = Service::with('category')->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'service' => $service,
        ]);
    }

    /**
     * =========================
     * UPDATE SERVICE
     * =========================
     */
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_code'        => 'required|string|max:50|unique:services,service_code,' . $service->id,
            'service_name'        => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'govt_fee'            => 'nullable|string|max:255',
            'our_fee'             => 'nullable|numeric|min:0',
            'is_taxable'          => 'required|boolean',
            'tax_percentage'      => 'nullable|required_if:is_taxable,1|numeric|min:0',
            'allow_discount'      => 'required|boolean',
            'visible_to_client'   => 'required|boolean',
            'processing_time'     => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'status'              => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $oldCategoryId = $service->service_category_id;

            $service->update([
                'service_code'        => $request->service_code,
                'service_name'        => $request->service_name,
                'service_category_id' => $request->service_category_id,
                'govt_fee'            => $request->govt_fee,
                'our_fee'             => $request->our_fee !== '' ? $request->our_fee : null,
                'is_taxable'          => $request->is_taxable,
                'tax_percentage'      => $request->is_taxable ? $request->tax_percentage : null,
                'allow_discount'      => $request->allow_discount,
                'visible_to_client'   => $request->visible_to_client,
                'processing_time'     => $request->processing_time,
                'description'         => $request->description,
                'status'              => $request->status,
            ]);

            if ($oldCategoryId != $request->service_category_id) {
                $this->updateCategoryCount($oldCategoryId);
                $this->updateCategoryCount($request->service_category_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'service' => $service->load('category'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
            ], 500);
        }
    }

    /**
     * =========================
     * DELETE SERVICE
     * =========================
     */
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $categoryId = $service->service_category_id;
            $service->forceDelete();

            $this->updateCategoryCount($categoryId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
            ], 500);
        }
    }

    /**
     * =========================
     * TOGGLE STATUS
     * =========================
     */
    public function updateStatus($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        $service->update([
            'status' => $service->status === 'active' ? 'inactive' : 'active',
        ]);

        return response()->json([
            'success'    => true,
            'new_status' => $service->status,
        ]);
    }

    /**
     * =========================
     * REORDER SERVICES
     * =========================
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id',
            'services.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($request->services as $item) {
                Service::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Services reordered successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder services',
            ], 500);
        }
    }

    /**
     * =========================
     * HELPERS
     * =========================
     */
    private function updateCategoryCount($categoryId)
    {
        ServiceCategory::where('id', $categoryId)->update([
            'services_count' => Service::where('service_category_id', $categoryId)
                ->whereNull('deleted_at')
                ->count()
        ]);
    }

    private function generateServiceCode()
    {
        $last = Service::latest('id')->first();
        $number = $last ? intval(substr($last->service_code, 4)) + 1 : 1;

        return 'SER-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}