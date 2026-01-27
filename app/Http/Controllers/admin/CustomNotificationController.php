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
use App\Services\NotificationService;

class CustomNotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
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
        ])->latest();

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('image', function ($row) {

                if (!$row->image) {
                    return '-';
                }

                $imageUrl = asset('uploads/custom_notifications/' . $row->image);

                return '
        <img src="' . $imageUrl . '"
             width="60"
             height="60"
             style="cursor:pointer;border-radius:6px"
             onclick="openImageModal(\'' . $imageUrl . '\')">
    ';
            })


            ->addColumn('category', fn($row) => $row->category?->name ?? '-')
            ->addColumn('subcategory', fn($row) => $row->subcategory?->name ?? '-')
            ->addColumn('product', fn($row) => $row->product?->name ?? '-')
            ->addColumn('description', fn($row) => $row->description ?? '-')

            ->addColumn('action', function ($row) {

                $buttons = '';

                // ✏️ EDIT
                if (auth()->user()->can('PushNotification-Edit')) {
                    $buttons .= '
            <a href="' . route('admin.custom-notifications.edit', $row->id) . '"
               class="btn btn-sm btn-primary mr-1">
               Edit
            </a>
        ';
                }

                // 🗑 DELETE
                if (auth()->user()->can('PushNotification-Delete')) {
                    $buttons .= '
            <button class="btn btn-sm btn-danger mr-1"
                onclick="deleteNotification(' . $row->id . ')">
                Delete
            </button>
        ';
                }

                // 🔁 RESEND
                if (auth()->user()->can('PushNotification-Resend')) {
                    $buttons .= '
            <button class="btn btn-sm btn-success"
                onclick="resendnotification(' . $row->id . ')">
                Resend
            </button>
        ';
                }

                return $buttons ?: '-';
            })


            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    /* =====================================================
     | CREATE
     ===================================================== */
    public function create()
    {
        $categories = Category::pluck('name', 'id');

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
            'product_id'     => 'required',
            'state'          => 'required|string',
            'city'           => 'required|string',
            'customer_id'    => 'required|array',
        ]);

        /* IMAGE UPLOAD */
        $imageName = null;
        if ($request->hasFile('image')) {
            $path = public_path('uploads/custom_notifications');
            if (!\File::exists($path)) {
                \File::makeDirectory($path, 0755, true);
            }
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move($path, $imageName);
        }

        /* SAVE CUSTOM NOTIFICATION */
        $customNotification = CustomNotification::create([
            'title'          => $request->title,
            'description'    => $request->description,
            'image'          => $imageName,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'product_id'     => $request->product_id,
            'state'          => $request->state,
            'city'           => $request->city,
            'customer_id'    => json_encode($request->customer_id),
        ]);
        $imageUrl = $imageName
            ? asset('uploads/custom_notifications/' . $imageName)
            : null;
        /* 🔔 SEND PUSH TO SELECTED CUSTOMERS */
        $customers = Customer::whereIn('id', $request->customer_id)->get();

        foreach ($customers as $customer) {
            NotificationService::send(
                $customer->id,                     // customerId
                $customNotification->title,        // title
                $customNotification->description,  // message
                'custom_notification',             // type
                $customNotification->id,            // reference id
                $customer->fcm_token,
                $request->category_id,
                $request->subcategory_id,
                $request->product_id,
                $imageUrl
            );
        }

        return redirect()
            ->route('admin.custom-notifications.index')
            ->with('success', 'Notification sent successfully');
    }



    /* =====================================================
     | EDIT
     ===================================================== */
    public function edit($id)
    {
        $notification = CustomNotification::findOrFail($id);
        $categories   = Category::pluck('name', 'id');

        return view('admin.custom_notifications.edit', compact(
            'notification',
            'categories'
        ));
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, $id)
    {
        $notification = CustomNotification::findOrFail($id);

        /* ---------------------------------
     | 1. Validate
     --------------------------------- */
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'image'          => 'nullable|image|max:2048',
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'product_id'     => 'required|exists:products,id',
            'customer_id'    => 'required|array',
            'state'          => 'required|string',
            'city'           => 'required|string',
        ]);

        /* ---------------------------------
     | 2. Image Update (if changed)
     --------------------------------- */
        if ($request->hasFile('image')) {
            $oldPath = public_path('uploads/custom_notifications/' . $notification->image);

            if ($notification->image && File::exists($oldPath)) {
                File::delete($oldPath);
            }

            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(
                public_path('uploads/custom_notifications'),
                $imageName
            );

            $notification->image = $imageName;
        }
        $imageUrl = $notification->image
            ? asset('uploads/custom_notifications/' . $notification->image)
            : null;
        /* ---------------------------------
     | 3. Update Notification Record
     --------------------------------- */
        $notification->update([
            'title'          => $request->title,
            'description'    => $request->description,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'product_id'     => $request->product_id,
            'state'          => $request->state,
            'city'           => $request->city,
            'customer_id'    => json_encode($request->customer_id),
        ]);

        /* ---------------------------------
     | 4. 🔔 RESEND NOTIFICATION (UPDATED)
     --------------------------------- */
        $customers = Customer::whereIn('id', $request->customer_id)->get();

        foreach ($customers as $customer) {
            NotificationService::send(
                $customer->id,                    // customer id
                $notification->title,             // title
                $notification->description,       // message
                'custom_notification',             // type          // reference id
                $customer->fcm_token,
                $request->category_id,
                $request->subcategory_id,
                $request->product_id,
                $imageUrl          // fcm token
            );
        }

        /* ---------------------------------
     | 5. Redirect
     --------------------------------- */
        return redirect()
            ->route('admin.custom-notifications.index')
            ->with('success', 'Custom notification updated & resent successfully');
    }

    /* =====================================================
     | DELETE
     ===================================================== */
    public function destroy($id)
    {

        $notification = CustomNotification::findOrFail($id);

        if ($notification->image) {
            $file = public_path('uploads/custom_notifications/' . $notification->image);
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
        return Subcategory::where('category_id', $categoryId)
            ->pluck('name', 'id');
    }

    // Subcategory → Products
    public function getProducts($subcategoryId)
    {
        return Product::where('subcategory_id', $subcategoryId)
            ->pluck('name', 'id');
    }

    // Load States
    public function states()
    {
        return response()->json([
            "Gujarat",
            "Maharashtra",
            "Rajasthan",
            "Delhi"
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


    public function customersByCity(Request $request)
    {

        $request->validate([
            'city' => 'required|string'
        ]);

        return Customer::where('city', $request->city)
            ->where('status', 1) 
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    public function resend(CustomNotification $custom_notification)
    {
        $customerIds = json_decode($custom_notification->customer_id, true) ?? [];

        if (empty($customerIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No customers found'
            ], 400);
        }

        // Build image URL
        $imageUrl = $custom_notification->image
            ? asset('uploads/custom_notifications/' . $custom_notification->image)
            : null;

        $customers = Customer::whereIn('id', $customerIds)->get();

        foreach ($customers as $customer) {
            NotificationService::send(
                $customer->id,
                $custom_notification->title,
                $custom_notification->description,
                'custom_notification',
                $custom_notification->id,
                $customer->fcm_token,
                $imageUrl,
                $custom_notification->category_id,
                $custom_notification->subcategory_id,
                $custom_notification->product_id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification resent successfully'
        ]);
    }
}
