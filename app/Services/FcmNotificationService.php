<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\User;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send notification to a specific device token
     */
    public function sendToToken(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        try {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body))
                ->withData(array_map('strval', $data));

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Send Error', ['error' => $e->getMessage(), 'token' => $deviceToken]);
            return false;
        }
    }

    /**
     * Send notification to a specific User
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        // 1. Log to database
        NotificationModel::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'type' => $data['type'] ?? 'general',
            'is_read' => false,
        ]);

        // 2. Send via FCM if token exists
        if ($user->fcm_token) {
            return $this->sendToToken($user->fcm_token, $title, $body, $data);
        }

        return false;
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData(array_map('strval', $data));

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Topic Send Error', ['error' => $e->getMessage(), 'topic' => $topic]);
            return false;
        }
    }
}
