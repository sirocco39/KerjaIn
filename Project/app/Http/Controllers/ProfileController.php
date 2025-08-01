<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon; 


class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user(); 
        return view('profile', compact('user'));
    }

    public function show($id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            abort(404); 
        }
        

        return view('job-requester.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::id(); 
        $user = User::find($user);


        
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'phone_number'   => 'nullable|string|max:20',
            'birth_date'     => 'nullable|date',
            'photo_url_user' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        
        $firstName = $request->first_name;
        $lastName = $request->last_name;


        
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->phone_number = $request->phone_number;
        $user->birth_date = $request->birth_date;
        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy(Auth::id())
            ->log('User ' . $user->first_name . ' ' . $user->last_name . ' updated their profile on ' . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . '.'); 

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updatePhoto(Request $request)
    {

        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::id(); 
        $user = User::find($user);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/profile_photos', $photoName);
            $user->photo_url_user = 'storage/profile_photos/' . $photoName;
            $user->save();

            activity()
                ->performedOn($user)
                ->causedBy(Auth::id())
                ->log('User ' . $user->first_name . ' ' . $user->last_name . ' updated their profile photo on ' . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . '.'); 

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Foto tidak valid.');
    }

    public function uploadWorkerPhoto(Request $request)
    {
        $request->validate([
            'photo_url' => 'required|image|mimes:jpg,jpeg,png|max:5120', 
        ]);

        $user = Auth::id(); 
        $user = User::find($user);

        
        if ($request->hasFile('photo_url')) {
            $file = $request->file('photo_url');
            $filename = 'worker_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads/worker_photos', $filename); 

            
            $user->photo_url_worker = 'storage/uploads/worker_photos/' . $filename;
            $user->save();

            activity()
                ->performedOn($user)
                ->causedBy(Auth::id())
                ->log('Worker ' . $user->first_name . ' ' . $user->last_name . ' uploaded their worker photo on ' . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . '.'); 
        }

        return redirect()->back()->with('success', 'Foto berhasil diunggah!');
    }


}
