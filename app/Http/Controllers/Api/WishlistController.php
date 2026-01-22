<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use Laravel\Sanctum\PersonalAccessToken;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        $customer = $accessToken->tokenable;

        $wishlist = Wishlist::firstOrCreate([
            'customer_id' => $customer->id,
            'product_id'  => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Product added to wishlist',
            'data'    => $wishlist
        ]);
    }

    public function list(Request $request)
    {
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        $customer = $accessToken->tokenable;

        $wishlist = Wishlist::with('product')
            ->where('customer_id', $customer->id)
            ->get()
            ->map(function ($item) {
                return [
                    'wishlist_id' => $item->id,
                    'product' => [
                        'id'     => $item->product->id,
                        'name'   => $item->product->name,
                        'size'  => $item->product->size,
                        'weight'  => $item->product->weight,
                        'hole_size'  => $item->product->hole_size,
                        'images' => $item->product->image_url,
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Wishlist fetched successfully',
            'data'    => $wishlist
        ]);
    }

    public function remove(Request $request)
    {
        // Get wishlist_id from query or body
        $wishlist_id = $request->query('id'); // or $request->wishlist_id

        if (!$wishlist_id) {
            return response()->json([
                'success' => false,
                'code'    => 400,
                'message' => 'wishlist_id is required',
            ], 400);
        }

        $accessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Invalid or missing token',
            ], 401);
        }

        $customer = $accessToken->tokenable;

        $wishlist = Wishlist::where('id', $wishlist_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'message' => 'Wishlist item not found',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Item removed from wishlist',
        ]);
    }
}
