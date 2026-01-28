<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\CustomOrder;
use App\Models\QrProduct;
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

        QrProduct::where('customer_id', $customer->id)
            ->where('is_save', 1)
            ->update([
                'is_save' => 0,
            ]);

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

        if (!$accessToken || !($accessToken->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $customer = $accessToken->tokenable;

        $orders = Order::where('customer_id', $customer->id)
            ->select('order_id', 'status', 'remarks', 'created_at')
            ->groupBy('order_id', 'status', 'remarks', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {

                $items = Order::with('product')
                    ->where('order_id', $order->order_id)
                    ->get();

                $firstProduct = $items->first()?->product;

                $image = null;

                if ($firstProduct) {

                    // ✅ HANDLE GALLERY (JSON or ARRAY)
                    if (is_array($firstProduct->gallery)) {
                        $image = asset('uploads/products/' . $firstProduct->gallery[0]);
                    } elseif (is_string($firstProduct->gallery)) {
                        $decoded = json_decode($firstProduct->gallery, true);
                        if (is_array($decoded) && count($decoded)) {
                            $image = asset('uploads/products/' . $decoded[0]);
                        }
                    } elseif (!empty($firstProduct->image)) {
                        $image = asset('uploads/products/' . $firstProduct->image);
                    }
                }

                return [
                    'order_id'       => $order->order_id,
                    'status'         => $order->status,
                    'remarks'        => $order->remarks,
                    'total_quantity' => $items->sum('quantity'),
                    'total_weight'   => $items->sum('weight'),
                    'total_products' => $items->count(),
                    'image_url'      => $image, // ✅ IMAGE ADDED
                    'order_date'     => $order->created_at->format('d M Y h:m a'),
                ];
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Orders fetched successfully',
            'data'    => $orders,
        ]);
    }



    public function orderDetails(Request $request)
    {
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        $customer = $accessToken->tokenable;

        $orders = Order::with(['product.category'])
            ->where('order_id', $request->order_id)
            ->where('customer_id', $customer->id)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'message' => 'Order not found'
            ], 404);
        }

        $orderInfo = $orders->first();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Order details fetched successfully',
            'data'    => [
                'order_id'       => $request->order_id,
                'status'         => $orderInfo->status,
                'remarks'        => $orderInfo->remarks,
                'total_quantity' => $orders->sum('quantity'),
                'total_weight'   => $orders->sum('weight'),

                'products' => $orders->map(function ($order) {

                    $product = $order->product;

                    // ✅ HANDLE SINGLE / MULTIPLE IMAGES SAFELY
                    $images = [];

                    if (!empty($product->gallery)) {
                        // JSON images
                        $decoded = is_array($product->gallery)
                            ? $product->gallery
                            : json_decode($product->gallery, true);

                        $images = collect($decoded)->map(
                            fn($img) => asset('uploads/products/' . $img)
                        )->values();
                    } elseif (!empty($product->image)) {
                        // Single image
                        $images[] = asset('uploads/products/' . $product->gallery);
                    }

                    return [
                        'product_id' => $product->id,
                        'name'       => $product->name,
                        'category'   => $product->category->name ?? null,
                        'quantity'   => $order->quantity,
                        'weight'     => $order->weight,

                        // ✅ NEW
                        'images'     => $images,
                    ];
                }),
            ]
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

            $imagePath = 'uploads/custom_orders/' . $imageName;
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
                        ? asset($order->image)
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
