<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    public function switch($locale)
    {
        
        if (in_array($locale, ['id', 'en'])) {
            
            session()->put('locale', $locale);
            
            return redirect()->back()->with('custom_info_alert', __('alerts.bahasa_diubah', ['locale' => $locale == 'id' ? 'Indonesia' : 'English']));
        }

        
        return redirect()->back()->with('custom_error_alert', __('alerts.bahasa_tidak_didukung'));
    }
}
