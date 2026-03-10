<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class ClubLandingController extends Controller
{
    public function show(): View
    {
        $club = app('currentClub');

        $clubId   = $club->id;
        $settings = Setting::getAllCached($clubId);

        // Which sections are enabled (default on)
        $sections = [
            'hero'    => ($settings['page_hero_enabled']    ?? '1') === '1',
            'about'   => ($settings['page_about_enabled']   ?? '1') === '1',
            'contact' => ($settings['page_contact_enabled'] ?? '1') === '1',
            'social'  => ($settings['page_social_enabled']  ?? '1') === '1',
            'cta'     => ($settings['page_cta_enabled']     ?? '1') === '1',
        ];

        // Per-club logo (from settings) — falls back to club->logo
        $clubLogo = !empty($settings['logo'])
            ? asset('storage/' . $settings['logo'])
            : ($club->logo ? asset('storage/' . $club->logo) : null);

        return view('club-landing.index', compact('club', 'sections', 'clubLogo', 'settings'));
    }
}
