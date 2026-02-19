<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archer;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    const GOOGLE_FONTS = [
        'Inter', 'Roboto', 'Open Sans', 'Lato', 'Poppins',
        'Nunito', 'Montserrat', 'Raleway', 'Ubuntu',
        'Merriweather', 'Playfair Display', 'Oswald', 'Lexend',
    ];

    const HEADING_SIZES = [
        '16' => 'Small (16px)',
        '18' => 'Medium (18px)',
        '20' => 'Large (20px)',
        '24' => 'X-Large (24px)',
        '28' => 'XX-Large (28px)',
        '32' => 'Huge (32px)',
    ];

    public function index(): View
    {
        $settings      = Setting::getAllCached();
        $recentArchers = Archer::with('user')->latest()->take(6)->get();
        $totalArchers  = Archer::count();
        $newThisMonth  = Archer::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->count();

        return view('admin.settings.index', [
            'settings'      => $settings,
            'googleFonts'   => self::GOOGLE_FONTS,
            'headingSizes'  => self::HEADING_SIZES,
            'recentArchers' => $recentArchers,
            'totalArchers'  => $totalArchers,
            'newThisMonth'  => $newThisMonth,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'logo'         => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'body_font'    => ['nullable', 'string', 'max:100'],
            'heading_font' => ['nullable', 'string', 'max:100'],
            'heading_size' => ['nullable', 'string', 'max:10'],
        ]);

        if ($request->hasFile('logo')) {
            $old = Setting::get('logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo', $path);
        }

        foreach (['body_font', 'heading_font', 'heading_size'] as $key) {
            Setting::set($key, $request->input($key));
        }

        cache()->forget('site_settings');

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    public function removeLogo(): RedirectResponse
    {
        $logo = Setting::get('logo');
        if ($logo) Storage::disk('public')->delete($logo);
        Setting::set('logo', null);
        cache()->forget('site_settings');

        return redirect()->back()->with('success', 'Logo removed.');
    }
}
