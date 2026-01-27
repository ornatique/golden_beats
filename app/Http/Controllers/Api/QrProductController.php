<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrProduct;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class QrProductController extends Controller
{
    /**
     * ---------------------------------------
     * Resolve customer from bearer token
     * ---------------------------------------
     */
    private function getCustomer(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());

        if (!$token || !($token->tokenable instanceof \App\Models\Customer)) {
            return null;
        }

        return $token->tokenable;
    }

    /**
     * ---------------------------------------
     * 1️⃣ QR SAVE LIST
     * ---------------------------------------
     */
    public function index(Request $request)
    {
        $customer = $this->getCustomer($request);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = QrProduct::with('product')
            ->where('customer_id', $customer->id)
            ->where('is_save', 1)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'scanned_name' => $item->scanned_name,
                    'product'      => $item->product,
                    'created_at'   => $item->created_at->format('d M Y h:i A'),
                ];
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => $data->isEmpty()
                ? 'No QR products found'
                : 'QR products fetched successfully',
            'data'    => $data,
        ], 200);
    }

    /**
     * ---------------------------------------
     * 2️⃣ SAVE QR PRODUCT (QR SCAN)
     * ---------------------------------------
     * QR payload example:
     * { "name": "KH60" }
     */
    public function store(Request $request)
    {
        $customer = $this->getCustomer($request);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'name' => 'required|string', // QR scanned value
        ]);

        $scannedValue = trim($request->name);
        
        /* ---------------------------------
     | 1. FIND PRODUCT (ID → NAME)
     --------------------------------- */
        $product = null;

        // 🔹 If QR contains numeric ID
        // if (is_numeric($scannedValue)) {
        //     $product = Product::find($scannedValue);
        // }

        // 🔹 If not found, try by name
        // if (!$product) {
            $product = Product::where('name', $scannedValue)->first();
        // }

        /* ---------------------------------
     | 2. PREVENT DUPLICATE SAVE
     --------------------------------- */
        $exists = QrProduct::where('customer_id', $customer->id)
            ->where('scanned_name', $scannedValue)
            ->first();

        if ($exists) {
            return response()->json([
                'success' => true,
                'code'    => 200,
                'message' => 'QR already saved',
                'data'    => $exists->load('product'),
            ], 200);
        }

        /* ---------------------------------
     | 3. SAVE QR PRODUCT
     --------------------------------- */
        $qrProduct = QrProduct::create([
            'customer_id'  => $customer->id,
            'product_id'   => $product?->id, // ✅ set if found
            'scanned_name' => $scannedValue,  // always save QR value
            'is_save'      => 1,
        ]);

        /* ---------------------------------
     | 4. AUTO ADD TO CART (OPTIONAL)
     --------------------------------- */
        if ($product) {
            Cart::firstOrCreate(
                [
                    'customer_id' => $customer->id,
                    'product_id'  => $product->id,
                ],
                [
                    'quantity' => 1,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'code'    => 201,
            'message' => $product
                ? 'QR saved and product linked'
                : 'QR saved (product not found)',
            'data'    => $qrProduct->load('product'),
        ], 201);
    }


    /**
     * ---------------------------------------
     * 3️⃣ DELETE ALL QR SAVES (USER WISE)
     * ---------------------------------------
     */
    public function destroy(Request $request)
    {
        $customer = $this->getCustomer($request);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        QrProduct::where('customer_id', $customer->id)->delete();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'All QR saved products deleted',
        ], 200);
    }
}
