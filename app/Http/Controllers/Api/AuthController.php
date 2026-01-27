<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Category;
use App\Models\BannerAd;
use App\Models\PopupBannerAd;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Wishlist;

class AuthController extends Controller
{
    /* =====================================================
     | REGISTER
     ===================================================== */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:customers,email',
            'number'       => 'required|unique:customers,number',
            'password'     => 'required|min:6',
            'state'        => 'required',
            'city'         => 'required',
            'image'        => 'nullable|image',
            'company_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => 422,
                'error'   => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        /* Image Upload */
        $imagePath = null;
        if ($request->hasFile('image')) {
            $destination = public_path('uploads/customer');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move($destination, $imageName);
            $imagePath = $imageName;
        }

        /* Create Customer */
        $customer = Customer::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'number'       => $request->number,
            'company_name' => $request->company_name,
            'state'        => $request->state,
            'city'         => $request->city,
            'image'        => $imagePath,
            'status'       => 0,
            'device_key' => $request->device_key,
            'fcm_token' => $request->fcm_token,
            'device_type' => $request->device_type
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'code'    => 201,
            'message' => 'Registration successful',
            'token'   => $token,
            'data'    => $customer,
        ], 201);
    }

    /* =====================================================
     | LOGIN
     ===================================================== */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number'   => 'required',
            'password' => 'required',
            // 'device_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => 422,
                'error'   => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $customer = Customer::where('number', $request->number)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'error'   => 'CUSTOMER_NOT_FOUND',
                'message' => 'Customer not found',
            ], 404);
        }

        if ($customer->status != 1) {
            return response()->json([
                'success' => false,
                'code'    => 403,
                'error'   => 'ACCOUNT_INACTIVE',
                'message' => 'Account is inactive',
            ], 403);
        }

        if (!Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'INVALID_CREDENTIALS',
                'message' => 'Invalid login credentials',
            ], 401);
        }

        /* Single Device Login */
        if (!$customer->device_key) {
            // first time login
            $customer->device_key = $request->device_key;
            $customer->save();
        } elseif ($customer->device_key !== $request->device_key) {
            return response()->json([
                'success' => false,
                'code'    => 409,
                'error'   => 'DEVICE_CONFLICT',
                'message' => 'Already logged in on another device',
            ], 409);
        }

        $customer->device_key = $request->device_key;
        $customer->fcm_token    = $request->fcm_token;
        $customer->device_type  = $request->device_type;
        $customer->save();

        $customer->tokens()->delete();
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Login successful',
            'token'   => $token,
            'data'    => $customer,
        ]);
    }

    /* =====================================================
     | PROFILE
     ===================================================== */
    public function profile(Request $request)
    {
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'UNAUTHORIZED',
                'message' => 'Invalid or missing token',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Profile fetched successfully',
            'type'    => class_basename($accessToken->tokenable),
            'data'    => $accessToken->tokenable,
        ]);
    }

    /* =====================================================
     | LOGOUT
     ===================================================== */


    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'NO_TOKEN',
                'message' => 'Bearer token missing',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'INVALID_TOKEN',
                'message' => 'Token invalid',
            ], 401);
        }

        // ✅ GET LOGGED-IN USER
        $user = $accessToken->tokenable;

        // ✅ REMOVE DEVICE / FCM TOKEN
        $user->update([
            'device_key' => null, // or device_key
        ]);

        // ✅ DELETE ACCESS TOKEN
        $accessToken->delete();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Logout successful',
        ]);
    }


    /* =====================================================
     | STATES & CITIES
     ===================================================== */
    public function states()
    {
        return response()->json([
            'success' => true,
            'code'    => 200,
            'data'    => [ "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh",
        "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka",
        "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram",
        "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu",
        "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"],
        ]);
    }

    public function citiesByState(Request $request)
    {
        if (!$request->state) {
            return response()->json([
                'success' => false,
                'code'    => 422,
                'error'   => 'STATE_REQUIRED',
                'message' => 'State is required',
            ], 422);
        }

        $response = Http::withoutVerifying()->post(
            'https://countriesnow.space/api/v0.1/countries/state/cities',
            ['country' => 'India', 'state' => $request->state]
        );

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'code'    => 500,
                'error'   => 'CITY_FETCH_FAILED',
                'message' => 'Unable to fetch cities',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'code'    => 200,
            'data'    => $response->json('data'),
        ]);
    }
    public function dashboard(Request $request)
    {
        /* ---------------------------------
     | 1. Validate Token
     --------------------------------- */
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'TOKEN_MISSING',
                'message' => 'Authorization token missing',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !($accessToken->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'UNAUTHORIZED',
                'message' => 'Invalid token',
            ], 401);
        }

        $customer = $accessToken->tokenable;
        if ($request->filled('fcm_token')) {

            // Update only if changed (optional optimization)
            if ($customer->fcm_token !== $request->fcm_token) {
                $customer->update([
                    'fcm_token' => $request->fcm_token,
                ]);
            }
        }
        /* ---------------------------------
     | 2. Fetch Categories from category_ids
     --------------------------------- */
        $categoryIds = [];

        if (!empty($customer->category_ids)) {
            $categoryIds = json_decode($customer->category_ids, true) ?? [];
        }

        $categories = Category::whereIn('id', $categoryIds)
            ->get();

        /* ---------------------------------
     | 3. Fetch Banner Ads (ALL)
     --------------------------------- */
        $bannerAds = BannerAd::orderBy('id', 'desc')
            ->get();
        $popupBannerAd = PopupBannerAd::orderBy('id', 'desc')
            ->get();
        /* ---------------------------------
     | 4. Final Response
     --------------------------------- */
        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Dashboard data fetched successfully',
            'data'    => [
                'customer'   => $customer,
                'categories' => $categories,
                'banner_ads' => $bannerAds,
                'PopupBannerAd' => $popupBannerAd,
            ]
        ], 200);
    }


    public function subcategoriesByCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategories = Subcategory::where('category_id', $request->category_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Subcategories fetched successfully',
            'data'    => $subcategories,
        ], 200);
    }

    public function products(Request $request)
    {
        /* ---------------------------------
     | 1. Validate Token
     --------------------------------- */
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'TOKEN_MISSING',
                'message' => 'Authorization token missing',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !($accessToken->tokenable instanceof Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'UNAUTHORIZED',
                'message' => 'Invalid token',
            ], 401);
        }

        /** @var Customer $customer */
        $customer = $accessToken->tokenable;

        /* ---------------------------------
     | 2. Validate Request
     --------------------------------- */
        $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
        ]);

        /* ---------------------------------
     | 3. Check Customer Allowed Categories
     --------------------------------- */
        $allowedCategoryIds = json_decode($customer->category_ids, true) ?? [];

        if (!in_array($request->category_id, $allowedCategoryIds)) {
            return response()->json([
                'success' => false,
                'code'    => 403,
                'error'   => 'CATEGORY_NOT_ALLOWED',
                'message' => 'You are not allowed to access this category',
            ], 403);
        }

        /* ---------------------------------
     | 4. Fetch Wishlist Map (IMPORTANT PART)
     | product_id => wishlist_id
     --------------------------------- */
        $wishlistMap = Wishlist::where('customer_id', $customer->id)
            ->pluck('id', 'product_id')
            ->toArray();

        /* ---------------------------------
     | 5. Fetch Products
     --------------------------------- */
        $productsQuery = Product::where('category_id', $request->category_id);

        if ($request->filled('subcategory_id')) {
            $productsQuery->where('subcategory_id', $request->subcategory_id);
        }

        $products = $productsQuery
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($product) use ($wishlistMap) {

                $data = $product->toArray();

                $wishlistId = $wishlistMap[$product->id] ?? null;

                $data['is_wishlisted'] = $wishlistId !== null;
                $data['wishlist_id']  = $wishlistId;

                return $data;
            });

        /* ---------------------------------
     | 6. Response
     --------------------------------- */
        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Products fetched successfully',
            'data'    => $products,
        ], 200);
    }

    public function productDetails(Request $request)
    {
        $id = $request->query('id');
        $product = Product::with(['category', 'subcategory'])
            ->where('id', $id)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'error'   => 'PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Product details fetched successfully',
            'data'    => [
                'id'          => $product->id,
                'name'        => $product->name,
                'hole_size'       => $product->hole_size,
                'size'       => $product->size,
                'weight' => $product->weight,
                'category'    => $product->category,
                'subcategory' => $product->subcategory,
                'images'      => $product->image_url, // accessor
                'created_at'  => $product->created_at,
            ]
        ], 200);
    }

    public function search(Request $request)
    {
        /* ---------------------------------
     | 1. Get search keyword
     --------------------------------- */
        $keyword = trim($request->query('q'));

        /* ---------------------------------
     | 2. Base query
     --------------------------------- */
        $query = Product::query()
            ->orderBy('id', 'desc');

        /* ---------------------------------
     | 3. Apply search if keyword exists
     --------------------------------- */
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {

                // 🔹 Exact match FIRST (higher priority)
                $q->where('name', $keyword)

                    // 🔹 Partial match
                    ->orWhere('name', 'LIKE', "%{$keyword}%");
            });
        }

        /* ---------------------------------
     | 4. Fetch products
     --------------------------------- */
        $products = $query->get()->map(function ($product) {
            $data = $product->toArray();

            // optional image accessor
            $data['image_url'] = $product->image_url ?? null;

            return $data;
        });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Products fetched successfully',
            'count'   => $products->count(),
            'data'    => $products,
        ], 200);
    }
}
