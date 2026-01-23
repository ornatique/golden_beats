<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomNotification;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class CustomNotificationController extends Controller
{
    /* =====================================================
     | INDEX
     ===================================================== */
    public function index()
    {
        return view('admin.custom_notifications.index');
    }

    /* =====================================================
     | DATATABLE
     ===================================================== */
    public function getData(Request $request)
    {
        $query = CustomNotification::with([
            'category:id,name',
            'subcategory:id,name',
            'product:id,name',
            'user:id,name'
        ])->latest();

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('image', function ($row) {
                return $row->image
                    ? '<img src="'.$row->image_url.'" width="60">'
                    : '-';
            })

            ->addColumn('category', fn($row) => $row->category?->name ?? '-')
            ->addColumn('subcategory', fn($row) => $row->subcategory?->name ?? '-')
            ->addColumn('product', fn($row) => $row->product?->name ?? '-')
            ->addColumn('user', fn($row) => $row->user?->name ?? '-')

            ->addColumn('action', function ($row) {
                return '
                    <a href="'.route('admin.custom-notifications.edit',$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                    <button class="btn btn-sm btn-danger" onclick="deleteNotification('.$row->id.')">Delete</button>
                ';
            })

            ->rawColumns(['image','action'])
            ->make(true);
    }

    /* =====================================================
     | CREATE
     ===================================================== */
    public function create()
    {
        $categories = Category::pluck('name','id');

        return view('admin.custom_notifications.create', compact('categories'));
    }

    /* =====================================================
     | STORE
     ===================================================== */
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'image'          => 'required|image|max:2048',
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'product_id'     => 'required|exists:products,id',
            'user_id'        => 'required|exists:customers,id',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $path = public_path('uploads/custom_notifications');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $imageName = time().'_'.$request->image->getClientOriginalName();
            $request->image->move($path, $imageName);
        }

        CustomNotification::create([
            'title'          => $request->title,
            'description'    => $request->description,
            'image'          => $imageName,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'product_id'     => $request->product_id,
            'user_id'        => $request->user_id,
        ]);

        return redirect()
            ->route('admin.custom-notifications.index')
            ->with('success','Custom notification created successfully');
    }

    /* =====================================================
     | EDIT
     ===================================================== */
    public function edit($id)
    {
        $notification = CustomNotification::findOrFail($id);
        $categories   = Category::pluck('name','id');

        return view('admin.custom_notifications.edit', compact(
            'notification','categories'
        ));
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, $id)
    {
        $notification = CustomNotification::findOrFail($id);

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'image'          => 'nullable|image|max:2048',
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'product_id'     => 'required|exists:products,id',
            'user_id'        => 'required|exists:customers,id',
        ]);

        if ($request->hasFile('image')) {
            $old = public_path('uploads/custom_notifications/'.$notification->image);
            if ($notification->image && File::exists($old)) {
                File::delete($old);
            }

            $imageName = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/custom_notifications'), $imageName);
            $notification->image = $imageName;
        }

        $notification->update([
            'title'          => $request->title,
            'description'    => $request->description,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'product_id'     => $request->product_id,
            'user_id'        => $request->user_id,
        ]);

        return redirect()
            ->route('admin.custom-notifications.index')
            ->with('success','Custom notification updated successfully');
    }

    /* =====================================================
     | DELETE
     ===================================================== */
    public function destroy($id)
    {
        $notification = CustomNotification::findOrFail($id);

        if ($notification->image) {
            $file = public_path('uploads/custom_notifications/'.$notification->image);
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /* =====================================================
     | AJAX HELPERS
     ===================================================== */

    // Category → Subcategory
    public function getSubcategories($categoryId)
    {
        return Subcategory::where('category_id',$categoryId)
            ->pluck('name','id');
    }

    // Subcategory → Products
    public function getProducts($subcategoryId)
    {
        return Product::where('subcategory_id',$subcategoryId)
            ->pluck('name','id');
    }

    // Load States
    public function states()
    {
        return response()->json([
            "Gujarat","Maharashtra","Rajasthan","Delhi"
        ]);
    }

    // State → Cities
    public function cities(Request $request)
    {
        $response = Http::withoutVerifying()->post(
            'https://countriesnow.space/api/v0.1/countries/state/cities',
            [
                'country' => 'India',
                'state'   => $request->state
            ]
        );

        return response()->json($response->json('data'));
    }

    // City → Customers
    public function customersByCity(Request $request)
    {
        return Customer::where('city',$request->city)
            ->select('id','name')
            ->get();
    }
}
