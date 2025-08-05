<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function sendNotification($notifiables, string $notificationClass, array $notificationData = [], array $mailAttachments = [], array $channels = ['mail', 'database'])
    {
        if (!is_array($notifiables) && !$notifiables instanceof \Illuminate\Database\Eloquent\Collection) {
            $notifiables = [$notifiables];
        }

        foreach ($notifiables as $notifiable) {
            try {
                $notificationInstance = new $notificationClass(...$notificationData);

                if (!empty($mailAttachments) && method_exists($notificationInstance, 'toMail')) {
                    $notificationInstance->attachments = $mailAttachments;
                }

                $notificationInstance->via = fn() => $channels;
                Notification::send($notifiable, $notificationInstance);
                event(new NotificationSent($notifiable, $notificationInstance));

            } catch (\Exception $e) {
                Log::error('Notification failed: ' . $e->getMessage(), [
                    'notifiable' => $notifiable,
                    'notification' => $notificationClass,
                    'data' => $notificationData,
                ]);
            }
        }
    }

    public function sendBulkEmail(array $users, string $subject, string $message, array $attachments = [])
    {
        foreach ($users as $user) {
            try {
                Notification::send($user, new \App\Notifications\BulkEmailNotification($subject, $message, $attachments));
            } catch (\Exception $e) {
                Log::error('Bulk email failed: ' . $e->getMessage(), [
                    'user' => $user,
                    'subject' => $subject,
                    'message' => $message,
                ]);
            }
        }
    }

    public function markAsRead(User $user, $notificationId = null)
    {
        try {
            if ($notificationId) {
                $user->notifications()->where('id', $notificationId)->first()?->markAsRead();
            } else {
                $user->unreadNotifications->markAsRead();
            }
        } catch (\Exception $e) {
            Log::error('Mark as read failed: ' . $e->getMessage(), [
                'user' => $user,
                'notification_id' => $notificationId,
            ]);
        }
    }

    public function getUnreadNotifications(User $user)
    {
        return $user->unreadNotifications;
    }

    public function getAllNotifications(User $user)
    {
        return $user->notifications;
    }

    public function getUserNotificationPreferences(User $user)
    {
        return $user->notificationPreferences ?? [
            'email' => true,
            'database' => true,
        ];
    }

    public function setUserNotificationPreferences(User $user, array $preferences)
    {
        $user->notificationPreferences = $preferences;
        $user->save();
    }

    public function filterChannelsByUserPreferences($userID, array $channels)
    {
        $user = User::find($userID);
        $defaultPreferences = ['email' => true, 'database' => true];
        $userPreferences = $defaultPreferences;

        return array_filter($channels, fn($channel) => Arr::get($userPreferences, $channel, false));
    }
}
