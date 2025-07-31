<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Asumsi notifikasi terkait dengan User
use Illuminate\Notifications\DatabaseNotification; // Untuk notifikasi Eloquent
use Carbon\Carbon; // For logging timestamp
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // Ambil semua notifikasi dari semua user, atau filter sesuai kebutuhan admin
        // Ini akan mengambil notifikasi yang disimpan di database (jika Anda menggunakan Database Notifications)
        $notifications = DatabaseNotification::orderBy('created_at', 'desc')->paginate(10);

        // Jika Anda ingin mengambil notifikasi khusus untuk admin yang sedang login
        // $adminUser = auth()->user(); // Asumsi admin adalah user
        // $notifications = $adminUser->notifications()->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        if ($request->status == 'read') {
            $notification->markAsRead();
            activity()
                ->performedOn($notification) // Added this line
                ->causedBy(Auth::user())
                ->log("Notifikasi #{$notification->id} ditandai sebagai sudah dibaca oleh " . Auth::user()->first_name . " pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . ".");
            return redirect()->back()->with('success', __('admin/notifications.notification_status_updated_successfully'));
        } else {
            $notification->read_at = null; // Tandai sebagai belum terbaca
            $notification->save();
            activity()
                ->performedOn($notification)
                ->causedBy(Auth::user())
                ->log("Notifikasi #{$notification->id} ditandai sebagai belum dibaca oleh " . Auth::user()->first_name . " pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . ".");
            return redirect()->back()->with('success', __('admin/notifications.notification_status_updated_successfully'));
        }
    }

    public function destroy($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notificationId = $notification->id; // Capture ID before deletion
        $notification->delete();
        activity()
            ->performedOn($notification)
            ->causedBy(Auth::user())
            ->log("Notifikasi #{$notificationId} dihapus oleh " . Auth::user()->first_name . " pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . ".");
        return redirect()->back()->with('success', __('admin/notifications.notification_deleted_successfully'));
    }

    // Metode show, create, edit, store, update tidak diperlukan untuk resource ini jika hanya untuk daftar dan aksi langsung
    public function show($id)
    {
        abort(404);
    }
    public function create()
    {
        abort(404);
    }
    public function store(Request $request)
    {
        abort(404);
    }
    public function edit($id)
    {
        abort(404);
    }
    public function update(Request $request, $id)
    {
        abort(404);
    }
}
