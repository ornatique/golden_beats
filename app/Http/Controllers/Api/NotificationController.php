<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Laravel\Sanctum\PersonalAccessToken;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());

        if (!$token || !($token->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $customer = $token->tokenable;

        $notifications = Notification::where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $notifications,
        ]);
    }
}
