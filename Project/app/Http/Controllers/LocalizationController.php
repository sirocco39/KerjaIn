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
        }

        // Kembali ke halaman sebelumnya
        return redirect()->back();
    }
}