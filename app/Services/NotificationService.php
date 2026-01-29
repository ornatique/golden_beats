<?php

namespace App\Services;

use App\Models\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NotificationService
{
    /**
     * Send notification (DB + Firebase)
     */
    public static function send(
        int $customerId,
        string $title,
        string $message,
        ?string $type = null,
        ?int $referenceId = null,
        ?string $fcmToken = null,
        ?string $imageUrl = null,
        ?int $categoryId = null,
        ?int $subcategoryId = null,
        ?int $productId = null
    ): void {
        /* -----------------------------
         | 1️⃣ Save Notification in DB
         ----------------------------- */
    $categoryId    = $categoryId !== null ? (int) $categoryId : null;
    $subcategoryId = $subcategoryId !== null ? (int) $subcategoryId : null;
    $productId     = $productId !== null ? (int) $productId : null;
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
     * Firebase Push (FCM v1)
     */
private static function sendFirebase(
    string $token,
    string $title,
    string $message,
    ?string $imageUrl = null,
    ?int $categoryId = null,
    ?int $subcategoryId = null,
    ?int $productId = null
): void {
    $factory = (new Factory)
        ->withServiceAccount(config('firebase.credentials'));

    $messaging = $factory->createMessaging();

    // 📦 DATA-ONLY payload (Flutter friendly)
    $cloudMessage = CloudMessage::fromArray([
        'token' => $token,
        'data' => array_filter([
            'title'          => $title,
            'body'           => $message,
            'image'          => $imageUrl,
            'category_id'    => (string) $categoryId,
            'subcategory_id' => (string) $subcategoryId,
            'product_id'     => (string) $productId,
            'type'           => 'custom_notification',
            'click_action'   => 'FLUTTER_NOTIFICATION_CLICK',
        ]),
    ]);

    // 🚀 Send
    $messaging->send($cloudMessage);
}
}
