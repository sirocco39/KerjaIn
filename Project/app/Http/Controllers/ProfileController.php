<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Ambil user yang sedang login
        return view('profile', compact('user'));
    }

    public function show($id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            abort(404); // jika user tidak ditemukan
        }
        // dd($user); // Debugging: tampilkan data user

        return view('job-requester.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::id(); // Ambil user yang sedang login
        $user = User::find($user);


        // Validasi data
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'phone_number'   => 'nullable|string|max:20',
            'birth_date'     => 'nullable|date',
            'photo_url_user' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // BENAR
        $firstName = $request->first_name;
        $lastName = $request->last_name;


        // Update ke database
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->phone_number = $request->phone_number;
        $user->birth_date = $request->birth_date;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updatePhoto(Request $request)
    {

        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/profile_photos', $photoName);
            $user->photo_url_user = 'storage/profile_photos/' . $photoName;
            $user->save();

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Foto tidak valid.');
    }

}

