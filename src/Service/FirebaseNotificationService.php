<?php

// src/Service/FirebaseNotificationService.php
namespace App\Service;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Contract\Messaging as MessagingContract;

class FirebaseNotificationService
{
    private MessagingContract $messaging;

    public function __construct(MessagingContract $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification(string $token, string $title, string $body): string
    {
        $message = CloudMessage::withTarget($token)
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ]);

        return $this->messaging->send($message);
    }
}
