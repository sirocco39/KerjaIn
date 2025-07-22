<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    public function switch($locale)
    {
        // Cek apakah bahasa yang diminta didukung
        if (in_array($locale, ['id', 'en'])) {
            // Simpan pilihan bahasa ke dalam session
            session()->put('locale', $locale);
            // Changed to custom alert
            return redirect()->back()->with('custom_info_alert', 'Bahasa berhasil diubah ke ' . ($locale == 'id' ? 'Indonesia' : 'English') . '.');
        }

        // Kembali ke halaman sebelumnya (with an error if locale not supported)
        return redirect()->back()->with('custom_error_alert', 'Bahasa tidak didukung.');
    }
}
