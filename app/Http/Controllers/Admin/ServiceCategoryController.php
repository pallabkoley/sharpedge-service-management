<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    /**
     * Index page (ONLY page)
     */
    public function index()
    {
        $categories = ServiceCategory::ordered()->paginate(12);

        return view('admin.service-categories.index', [
            'categories'         => $categories,
            'totalCategories'    => ServiceCategory::count(),
            'activeCategories'   => ServiceCategory::active()->count(),
            'inactiveCategories' => ServiceCategory::inactive()->count(),
            'totalServices'      => ServiceCategory::sum('services_count'),
        ]);
    }

    /**
     * 🔥 AJAX: Return services list as HTML (for popup)
     * NO new page, NO new blade
     */
  public function servicesPopup($id)
{
    $category = ServiceCategory::with(['services' => function ($q) {
        $q->orderBy('order');
    }])->findOrFail($id);

    // Build HTML manually (SAFE + SIMPLE)
    $html = '';

    if ($category->services->count() > 0) {
        $html .= '<table class="table table-bordered table-sm">';
        $html .= '<thead>
                    <tr>
                        <th>Code</th>
                        <th>Service Name</th>
                        <th>Status</th>
                    </tr>
                  </thead><tbody>';

        foreach ($category->services as $service) {
            $html .= '<tr>
                        <td>' . e($service->service_code) . '</td>
                        <td>' . e($service->service_name) . '</td>
                        <td>
                            <span class="badge bg-' . ($service->status === 'active' ? 'success' : 'secondary') . '">
                                ' . ucfirst($service->status) . '
                            </span>
                        </td>
                      </tr>';
        }

        $html .= '</tbody></table>';
    } else {
        $html = '<div class="text-center text-muted py-4">No services found in this category.</div>';
    }

    return response()->json([
        'success' => true,
        'html'    => $html
    ]);
}


    /**
     * Store category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        ServiceCategory::create([
            'category_code'  => $this->generateCategoryCode(),
            'name'           => $request->name,
            'slug'           => Str::slug($request->name),
            'status'         => $request->status,
            'display_order'  => ServiceCategory::max('display_order') + 1,
            'services_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
        ]);
    }

    /**
     * Update category
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $serviceCategory->update([
            'name'   => $request->name,
            'slug'   => Str::slug($request->name),
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
        ]);
    }

    /**
     * Delete category
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->services_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with services',
            ], 422);
        }

        $serviceCategory->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Generate category code
     */
    private function generateCategoryCode()
    {
        $last = ServiceCategory::latest('id')->first();
        $number = $last ? intval(substr($last->category_code, 4)) + 1 : 1;

        return 'CAT-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
