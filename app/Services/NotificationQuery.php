<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class NotificationQuery
{
    /** Notifications belong to the logged-in user only. */
    public static function forUser(User $user): Builder
    {
        return Notification::query()->where('user_id', $user->id);
    }

    public static function format(Notification $notification): array
    {
        return [
            'id' => (int) $notification->id,
            'title' => (string) $notification->title,
            'message' => (string) $notification->message,
            'link' => (string) ($notification->link ?? '#'),
            'is_read' => (bool) $notification->is_read,
            'type' => (string) $notification->type,
            'created_at' => $notification->created_at?->toIso8601String()
                ?? (string) $notification->created_at,
        ];
    }
}
