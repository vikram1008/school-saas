<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markRead(string $notificationId): RedirectResponse|JsonResponse
    {
        $user = Auth::guard('tenant')->user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }

        $url = $notification?->data['url'] ?? route('tenant.leave.index');

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect($url);
    }

    public function markAllRead(): RedirectResponse
    {
        Auth::guard('tenant')->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
