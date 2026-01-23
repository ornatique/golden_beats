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
        string $fcmToken = null
    ) {
        // 1️⃣ Save in DB
        Notification::create([
            'customer_id' => $customerId,
            'title'       => $title,
            'message'     => $message,
            'type'        => $type,
            'reference_id'=> $referenceId,
        ]);

        // 2️⃣ Send Firebase push (if token exists)
        if ($fcmToken) {
            self::sendFirebase($fcmToken, $title, $message);
        }
    }

    /**
     * Firebase Push
     */
    private static function sendFirebase(string $token, string $title, string $message)
    {
        Http::withHeaders([
            'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body'  => $message,
            ],
            'data' => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);
    }
}
