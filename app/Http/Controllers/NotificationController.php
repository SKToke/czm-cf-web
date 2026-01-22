<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, $id)
    {
        $notification = UserNotification::where(['notification_id' => $id, 'user_id' => auth()->user()->id])->first();

        $notification->read_at = now();
        $notification->save();

        return redirect()->back()->with('success', 'Notification marked as read successfully.');
    }
}
