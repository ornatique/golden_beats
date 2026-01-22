<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\CustomOrder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\PersonalAccessToken;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        /* ---------------------------------
     | 1. Validate Token
     --------------------------------- */
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (!$accessToken || !($accessToken->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $customer = $accessToken->tokenable;
        $remarks = $request->input('remarks');
        /* ---------------------------------
     | 2. Fetch Cart Items
     --------------------------------- */
        $cartItems = Cart::where('customer_id', $customer->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'code'    => 400,
                'message' => 'Cart is empty',
            ], 400);
        }

        /* ---------------------------------
     | 3. Generate Order ID (same for all)
     --------------------------------- */
        $orderId = 'ORD-' . strtoupper(Str::random(8));

        /* ---------------------------------
     | 4. Create Orders (ONE PER PRODUCT)
     --------------------------------- */
        foreach ($cartItems as $item) {
            Order::create([
                'order_id'   => $orderId,
                'product_id' => $item->product_id,
                'customer_id'    => $customer->id,
                'quantity'   => $item->quantity,
                'weight'     => $item->product->weight ?? null,
                'status'     => 'pending',
                'remarks'    => $remarks ?? null,
            ]);
        }

        /* ---------------------------------
     | 5. Clear Cart (PERMANENT)
     --------------------------------- */
        Cart::where('customer_id', $customer->id)->delete();

        return response()->json([
            'success'  => true,
            'code'     => 200,
            'message'  => 'Order placed successfully',
            'order_id' => $orderId,
        ]);
    }

    public function orderList(Request $request)
    {
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        $customer = $accessToken->tokenable;

        $orders = Order::with('product')
            ->where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Orders fetched successfully',
            'data'    => $orders,
        ]);
    }

    public function add_custom_order(Request $request)
    {
        /* ---------------------------------
     | 1. Validate Token
     --------------------------------- */
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (!$accessToken || !($accessToken->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $customer = $accessToken->tokenable;

        /* ---------------------------------
     | 2. Validate Request
     --------------------------------- */
        $request->validate([
            'image'       => 'required|image|max:5120',
            'remarks'     => 'nullable|string',
        ]);

        /* ---------------------------------
     | 3. Upload Image
     --------------------------------- */
        $imagePath = null;

        if ($request->hasFile('image')) {
            $destination = public_path('uploads/custom_orders');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $image      = $request->file('image');
            $imageName  = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destination, $imageName);

            $imagePath = 'uploads/custom_orders/' .$imageName;
        }

        /* ---------------------------------
     | 4. Create Custom Order
     --------------------------------- */
        $customOrder = CustomOrder::create([
            'user_id'     => $customer->id,
            'image'       => $imagePath,
            'description' => $request->description,
            'remarks'     => $request->remarks,
            'status'      => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'code'    => 201,
            'message' => 'Custom order submitted successfully',
            'data'    => $customOrder,
        ], 201);
    }

    public function list_custom_order(Request $request)
    {
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (!$accessToken || !($accessToken->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $customer = $accessToken->tokenable;

        $orders = CustomOrder::where('user_id', $customer->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id'          => $order->id,
                    'remarks'     => $order->remarks,
                    'status'      => $order->status,
                    'image_url'   => $order->image
                        ? asset('uploads/custom_orders/' . $order->image)
                        : null,
                    'created_at'  => $order->created_at->format('d M Y h:i A'),
                ];
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Custom orders fetched successfully',
            'data'    => $orders,
        ]);
    }
}
