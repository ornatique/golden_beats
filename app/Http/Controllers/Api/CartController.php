<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Customer;
use Laravel\Sanctum\PersonalAccessToken;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);
        $customer = $accessToken->tokenable;

        $cart = Cart::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            // Update quantity
            $cart->quantity += $request->quantity;
            $cart->save();
        } else {
            // Add new item
            $cart = Cart::create([
                'customer_id' => $customer->id,
                'product_id'  => $request->product_id,
                'quantity'    => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Product added to cart',
            'data'    => $cart
        ]);
    }

    public function cartList(Request $request)
    {
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);
        $customer = $accessToken->tokenable;

        $cartItems = Cart::with('product')
            ->where('customer_id', $customer->id)
            ->get()
            ->map(function ($cart) {
                return [
                    'cart_id'   => $cart->id,
                    'quantity'  => $cart->quantity,
                    'product'   => [
                        'id'    => $cart->product->id,
                        'name'  => $cart->product->name,
                        'size'  => $cart->product->size,
                        'weight'  => $cart->product->weight,
                        'hole_size'  => $cart->product->hole_size,
                        'images' => $cart->product->image_url,
                    ],
                    'total_price' => $cart->quantity * $cart->product->price
                ];
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Cart fetched successfully',
            'data'    => $cartItems
        ]);
    }

    public function removeFromCart(Request $request)
    {
        /* ---------------------------------
     | 1. Get cart_id from query
     --------------------------------- */
        $cart_id = $request->query('cart_id'); // or $request->cart_id

        if (!$cart_id) {
            return response()->json([
                'success' => false,
                'code'    => 400,
                'error'   => 'CART_ID_REQUIRED',
                'message' => 'cart_id is required',
            ], 400);
        }

        /* ---------------------------------
     | 2. Validate Token
     --------------------------------- */
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !($accessToken->tokenable instanceof Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'error'   => 'UNAUTHORIZED',
                'message' => 'Invalid token',
            ], 401);
        }

        $customer = $accessToken->tokenable;

        /* ---------------------------------
     | 3. Find Cart Item (belongs to customer)
     --------------------------------- */
        $cart = Cart::where('id', $cart_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'error'   => 'CART_ITEM_NOT_FOUND',
                'message' => 'Cart item not found',
            ], 404);
        }

        /* ---------------------------------
     | 4. Delete Cart Item
     --------------------------------- */
        $cart->delete();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Item removed from cart successfully',
        ], 200);
    }
}
