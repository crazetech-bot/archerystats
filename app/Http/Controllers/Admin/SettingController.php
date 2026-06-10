<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archer;
use App\Models\Club;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    const THEME_PRESETS = [
        'amber-navy'   => ['label' => 'Amber & Navy (Default)',   'primary' => '#f59e0b', 'primary_hover' => '#fbbf24', 'primary_light' => 'rgba(245,158,11,0.12)', 'sidebar' => '#0f172a', 'sidebar_hover' => '#1e293b', 'accent' => '#fbbf24'],
        'indigo-slate'  => ['label' => 'Indigo & Slate',           'primary' => '#6366f1', 'primary_hover' => '#818cf8', 'primary_light' => 'rgba(99,102,241,0.12)',  'sidebar' => '#1e293b', 'sidebar_hover' => '#334155', 'accent' => '#818cf8'],
        'emerald-gray'  => ['label' => 'Emerald & Charcoal',       'primary' => '#10b981', 'primary_hover' => '#34d399', 'primary_light' => 'rgba(16,185,129,0.12)', 'sidebar' => '#1f2937', 'sidebar_hover' => '#374151', 'accent' => '#34d399'],
        'rose-dark'     => ['label' => 'Rose & Dark',               'primary' => '#f43f5e', 'primary_hover' => '#fb7185', 'primary_light' => 'rgba(244,63,94,0.12)',  'sidebar' => '#18181b', 'sidebar_hover' => '#27272a', 'accent' => '#fb7185'],
        'sky-navy'      => ['label' => 'Sky Blue & Navy',           'primary' => '#0ea5e9', 'primary_hover' => '#38bdf8', 'primary_light' => 'rgba(14,165,233,0.12)', 'sidebar' => '#0f172a', 'sidebar_hover' => '#1e293b', 'accent' => '#38bdf8'],
        'violet-zinc'   => ['label' => 'Violet & Zinc',             'primary' => '#8b5cf6', 'primary_hover' => '#a78bfa', 'primary_light' => 'rgba(139,92,246,0.12)', 'sidebar' => '#27272a', 'sidebar_hover' => '#3f3f46', 'accent' => '#a78bfa'],
        'orange-brown'  => ['label' => 'Orange & Brown',            'primary' => '#f97316', 'primary_hover' => '#fb923c', 'primary_light' => 'rgba(249,115,22,0.12)', 'sidebar' => '#292524', 'sidebar_hover' => '#44403c', 'accent' => '#fb923c'],
        'teal-slate'    => ['label' => 'Teal & Slate',              'primary' => '#14b8a6', 'primary_hover' => '#2dd4bf', 'primary_light' => 'rgba(20,184,166,0.12)', 'sidebar' => '#1e293b', 'sidebar_hover' => '#334155', 'accent' => '#2dd4bf'],
        'custom'        => ['label' => 'Custom Colors',             'primary' => '',        'primary_hover' => '',        'primary_light' => '',                      'sidebar' => '',        'sidebar_hover' => '',        'accent' => ''],
    ];

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
        $settings      = Setting::getAllCached(null);
        $recentArchers = Archer::with('user')->latest()->take(6)->get();
        $totalArchers  = Archer::count();
        $newThisMonth  = Archer::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->count();
        $adminUsers    = \App\Models\User::whereIn('role', ['super_admin', 'club_admin', 'state_admin', 'national_team'])
                               ->orderBy('role')->orderBy('name')->get();
        $clubs         = Club::orderBy('name')->get();
        $archerUsers   = \App\Models\User::where('role', 'archer')
                               ->with('archer')->orderBy('name')->get();
        $coachUsers    = \App\Models\User::where('role', 'coach')
                               ->with('coach')->orderBy('name')->get();
        $clubAdminUsers = \App\Models\User::where('role', 'club_admin')
                               ->with('club')->orderBy('name')->get();

        return view('admin.settings.index', [
            'settings'       => $settings,
            'googleFonts'    => self::GOOGLE_FONTS,
            'headingSizes'   => self::HEADING_SIZES,
            'themePresets'   => self::THEME_PRESETS,
            'recentArchers'  => $recentArchers,
            'totalArchers'   => $totalArchers,
            'newThisMonth'   => $newThisMonth,
            'adminUsers'     => $adminUsers,
            'clubs'          => $clubs,
            'archerUsers'    => $archerUsers,
            'coachUsers'     => $coachUsers,
            'clubAdminUsers' => $clubAdminUsers,
            'regSettings'    => [
                'archer' => Setting::get('reg_archer_open', '1'),
                'coach'  => Setting::get('reg_coach_open',  '1'),
                'club'   => Setting::get('reg_club_open',   '1'),
            ],
            'regPolicy'      => [
                'expiry_days'  => Setting::get('reg_unverified_expiry_days', '7'),
                'club_mode'    => Setting::get('club_activation_mode', 'manual'),
            ],
        ]);
    }

    public function updateRegistration(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'  => ['required', 'in:archer,coach,club'],
            'value' => ['required', 'in:0,1'],
        ]);

        Setting::set('reg_' . $validated['type'] . '_open', $validated['value']);

        $label  = ['archer' => 'Archer', 'coach' => 'Coach', 'club' => 'Club'][$validated['type']];
        $status = $validated['value'] === '1' ? 'registration opened' : 'registration suspended';

        return redirect()->back()->with('success', "{$label} {$status} successfully.");
    }

    public function updateRegistrationPolicy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expiry_days' => ['required', 'integer', 'min:1', 'max:365'],
            'club_mode'   => ['required', 'in:manual,auto'],
        ]);

        Setting::set('reg_unverified_expiry_days', (string) $validated['expiry_days']);
        Setting::set('club_activation_mode', $validated['club_mode']);

        return redirect()->back()->with('success', 'Registration policy updated successfully.');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'logo'               => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'body_font'          => ['nullable', 'string', 'max:100'],
            'heading_font'       => ['nullable', 'string', 'max:100'],
            'heading_size'       => ['nullable', 'string', 'max:10'],
            'login_body_font'    => ['nullable', 'string', 'max:100'],
            'login_heading_font' => ['nullable', 'string', 'max:100'],
            'login_heading_size' => ['nullable', 'string', 'max:10'],
            'footer_text'        => ['nullable', 'string', 'max:500'],
            // SEO
            'seo_site_name'      => ['nullable', 'string', 'max:100'],
            'seo_description'    => ['nullable', 'string', 'max:300'],
            'seo_og_image'       => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'seo_ga_id'          => ['nullable', 'string', 'max:50'],
            'seo_gsc_token'      => ['nullable', 'string', 'max:200'],
            // Theme
            'theme_preset'       => ['nullable', 'string', 'max:30'],
            'theme_primary'      => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_primary_hover'=> ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_sidebar'      => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_sidebar_hover'=> ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_accent'       => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        if ($request->hasFile('logo')) {
            $old = Setting::get('logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo', $path);
        }

        if ($request->hasFile('seo_og_image')) {
            $old = Setting::get('seo_og_image');
            if ($old) Storage::disk('public')->delete($old);
            Setting::set('seo_og_image', $request->file('seo_og_image')->store('settings', 'public'));
        }

        foreach (['body_font', 'heading_font', 'heading_size',
                  'login_body_font', 'login_heading_font', 'login_heading_size',
                  'footer_text',
                  'seo_site_name', 'seo_description', 'seo_ga_id', 'seo_gsc_token',
                  'theme_preset', 'theme_primary', 'theme_primary_hover',
                  'theme_sidebar', 'theme_sidebar_hover', 'theme_accent'] as $key) {
            Setting::set($key, $request->input($key));
        }

        cache()->forget('site_settings_platform');

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    public function removeSeoImage(): RedirectResponse
    {
        $img = Setting::get('seo_og_image');
        if ($img) Storage::disk('public')->delete($img);
        Setting::set('seo_og_image', null);
        cache()->forget('site_settings_platform');

        return redirect()->back()->with('success', 'SEO image removed.');
    }

    public function removeLogo(): RedirectResponse
    {
        $logo = Setting::get('logo');
        if ($logo) Storage::disk('public')->delete($logo);
        Setting::set('logo', null);
        cache()->forget('site_settings_platform');

        return redirect()->back()->with('success', 'Logo removed.');
    }

    public function clubPage(): View
    {
        $club     = app('currentClub');
        $clubId   = $club->id;
        $settings = Setting::getAllCached($clubId);

        $clubLogo = !empty($settings['logo'])
            ? asset('storage/' . $settings['logo'])
            : ($club->logo ? asset('storage/' . $club->logo) : null);

        return view('settings.club-page', compact('club', 'settings', 'clubLogo'));
    }

    public function updateClubPage(Request $request): RedirectResponse
    {
        $club   = app('currentClub');
        $clubId = $club->id;

        $request->validate([
            'logo'            => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'tagline'         => ['nullable', 'string', 'max:200'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'contact_email'   => ['nullable', 'email', 'max:255'],
            'contact_phone'   => ['nullable', 'string', 'max:50'],
            'address'         => ['nullable', 'string', 'max:500'],
            'state'           => ['nullable', 'string', 'max:100'],
            'website'         => ['nullable', 'url', 'max:255'],
            'facebook_url'    => ['nullable', 'url', 'max:255'],
            'instagram_url'   => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
        ]);

        // Update club fields directly
        $club->update($request->only([
            'tagline', 'description',
            'contact_email', 'contact_phone',
            'address', 'state', 'website',
            'facebook_url', 'instagram_url', 'whatsapp_number',
        ]));

        // Club logo — stored per-club in settings
        if ($request->hasFile('logo')) {
            $old = Setting::get('logo', null, $clubId);
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('logo')->store('clubs/' . $clubId, 'public');
            Setting::set('logo', $path, $clubId);
        }

        // Section toggles — stored per-club in settings
        // Checkbox is absent when unchecked, but we send hidden input value=0 so it's always present
        foreach (['page_hero_enabled', 'page_about_enabled', 'page_contact_enabled',
                  'page_social_enabled', 'page_cta_enabled'] as $key) {
            Setting::set($key, $request->input($key, '0') ? '1' : '0', $clubId);
        }

        cache()->forget('site_settings_' . $clubId);

        return redirect()->back()->with('success', 'Club page settings saved.');
    }

    public function updatePopups(Request $request): RedirectResponse
    {
        $request->validate([
            'popup_manualcta_heading'       => ['nullable', 'string', 'max:200'],
            'popup_manualcta_body'          => ['nullable', 'string', 'max:500'],
            'popup_manualcta_scroll_pct'    => ['nullable', 'integer', 'min:1', 'max:100'],
            'popup_manualcta_time_s'        => ['nullable', 'integer', 'min:1', 'max:600'],
            'popup_manualcta_cooldown_h'    => ['nullable', 'integer', 'min:0', 'max:720'],
            'popup_manualcta_max_total'     => ['nullable', 'integer', 'min:0', 'max:99'],
            'popup_announcement_text'       => ['nullable', 'string', 'max:300'],
            'popup_announcement_delay_s'    => ['nullable', 'integer', 'min:1', 'max:60'],
            'popup_announcement_cooldown_d' => ['nullable', 'integer', 'min:0', 'max:365'],
            'popup_announcement_max_total'  => ['nullable', 'integer', 'min:0', 'max:99'],
            'popup_exitregister_heading'    => ['nullable', 'string', 'max:200'],
            'popup_exitregister_body'       => ['nullable', 'string', 'max:500'],
            'popup_exitregister_cooldown_h' => ['nullable', 'integer', 'min:0', 'max:720'],
            'popup_exitregister_max_total'  => ['nullable', 'integer', 'min:0', 'max:99'],
            'popup_helpsurvey_heading'      => ['nullable', 'string', 'max:200'],
            'popup_helpsurvey_body'         => ['nullable', 'string', 'max:500'],
            'popup_helpsurvey_inactivity_s' => ['nullable', 'integer', 'min:5', 'max:300'],
            'popup_helpsurvey_cooldown_h'   => ['nullable', 'integer', 'min:0', 'max:720'],
            'popup_helpsurvey_max_total'    => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $textKeys = [
            'popup_manualcta_heading', 'popup_manualcta_body',
            'popup_manualcta_scroll_pct', 'popup_manualcta_time_s',
            'popup_manualcta_cooldown_h', 'popup_manualcta_max_total',
            'popup_announcement_text', 'popup_announcement_delay_s',
            'popup_announcement_cooldown_d', 'popup_announcement_max_total',
            'popup_exitregister_heading', 'popup_exitregister_body',
            'popup_exitregister_cooldown_h', 'popup_exitregister_max_total',
            'popup_helpsurvey_heading', 'popup_helpsurvey_body',
            'popup_helpsurvey_inactivity_s', 'popup_helpsurvey_cooldown_h',
            'popup_helpsurvey_max_total',
        ];

        // Enabled checkboxes (absent from POST when unchecked)
        foreach (['popup_manualcta_enabled', 'popup_announcement_enabled',
                  'popup_exitregister_enabled', 'popup_helpsurvey_enabled'] as $key) {
            Setting::set($key, $request->has($key) ? '1' : '0');
        }

        foreach ($textKeys as $key) {
            Setting::set($key, $request->input($key));
        }

        cache()->forget('site_settings_platform');

        return redirect()->back()->with('success', 'Popup settings saved.');
    }
}
