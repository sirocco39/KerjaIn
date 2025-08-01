<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Notifications\DatabaseNotification; 
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        
        
        $notifications = DatabaseNotification::orderBy('created_at', 'desc')->paginate(10);

        
        
        

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        if ($request->status == 'read') {
            $notification->markAsRead();
            activity()
                ->performedOn($notification) 
                ->causedBy(Auth::user())
                ->log("Notifikasi #{$notification->id} ditandai sebagai sudah dibaca oleh " . Auth::user()->first_name . " pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . ".");
            return redirect()->back()->with('success', __('admin/notifications.notification_status_updated_successfully'));
        } else {
            $notification->read_at = null; 
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
        $notificationId = $notification->id; 
        $notification->delete();
        activity()
            ->performedOn($notification)
            ->causedBy(Auth::user())
            ->log("Notifikasi #{$notificationId} dihapus oleh " . Auth::user()->first_name . " pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . ".");
        return redirect()->back()->with('success', __('admin/notifications.notification_deleted_successfully'));
    }

    
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
