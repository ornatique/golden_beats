<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Send notification (DB + Firebase)
     */
    public static function send(
        int $customerId,
        string $title,
        string $message,
        string $type = null,
        int $referenceId = null,
        string $fcmToken = null,
        string $imageUrl = null,
        string $categoryId = null,
        string $subcategoryId = null,
        string $productId = null
    ) {
        /* -----------------------------
         | 1️⃣ Save Notification in DB
         ----------------------------- */
        Notification::create([
            'customer_id'  => $customerId,
            'title'        => $title,
            'message'      => $message,
            'type'         => $type,
            'reference_id' => $referenceId,
        ]);

        /* -----------------------------
         | 2️⃣ Send Firebase Push
         ----------------------------- */
        if ($fcmToken) {
            self::sendFirebase(
                $fcmToken,
                $title,
                $message,
                $imageUrl,
                $categoryId,
                $subcategoryId,
                $productId
            );
        }
    }

    /**
     * Firebase Push Notification
     */
    private static function sendFirebase(
        string $token,
        string $title,
        string $message,
        string $imageUrl = null,
        int $categoryId = null,
        string $subcategoryId = null,
        string $productId = null
    ) {
        $payload = [
            'to' => $token,

            // 🔔 Notification payload (visible)
            'notification' => [
                'title' => $title,
                'body'  => $message,
                'category_id'   => $categoryId,
                'subcategory_id'=> $subcategoryId,
                'product_id'    => $productId,
                'type'          => 'custom_notification',
            ],

            // 📦 Data payload (Flutter handling)
            'data' => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'image'         => $imageUrl,
                'category_id'   => $categoryId,
                'subcategory_id'=> $subcategoryId,
                'product_id'    => $productId,
                'type'          => 'custom_notification',
            ],
        ];

        /* -----------------------------
         | ✅ IMAGE SUPPORT
         ----------------------------- */
        if ($imageUrl) {
            $payload['notification']['image'] = $imageUrl;
            $payload['data']['image'] = $imageUrl; // Flutter background fix
        }

        Http::withHeaders([
            'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
            'Content-Type'  => 'application/json',
        ])->post(
            'https://fcm.googleapis.com/fcm/send',
            $payload
        );
    }
}
