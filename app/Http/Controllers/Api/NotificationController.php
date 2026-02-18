<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\CustomNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        /* ---------------------------------
     | 1️⃣ Get customer from token
     --------------------------------- */
        $token = PersonalAccessToken::findToken($request->bearerToken());

        if (!$token || !($token->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $customer = $token->tokenable;

        /* ---------------------------------
     | 2️⃣ Fetch notifications
     --------------------------------- */
        $notifications = Notification::where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($notification) {

                $imageUrl = null;

                // fetch image only for custom_notification
                if ($notification->type === 'custom_notification') {

                    $custom = CustomNotification::find($notification->reference_id);

                    if ($custom && $custom->image) {
                        $imageUrl = asset('uploads/custom_notifications/' . $custom->image);
                    }
                }

                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'reference_id' => $notification->reference_id,
                    'image' => $imageUrl, // ✅ proper image
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            });

        /* ---------------------------------
     | 3️⃣ Return response
     --------------------------------- */
        return response()->json([
            'success' => true,
            'count'   => $notifications->count(),
            'data'    => $notifications,
        ]);
    }
}
