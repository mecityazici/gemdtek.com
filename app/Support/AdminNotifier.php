<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class AdminNotifier
{
    /**
     * Notify every user who can access the admin panel (super_admin + editor).
     * Team captains intentionally excluded — they only see their own project.
     */
    public static function send(Notification $notification, array $roles = ['super_admin', 'editor']): int
    {
        $users = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', $roles))
            ->get();

        if ($users->isEmpty()) {
            return 0;
        }

        NotificationFacade::send($users, $notification);

        return $users->count();
    }
}
