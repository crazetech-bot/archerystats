{{--
│ Popup Templates — SportDNS
│ Registered via PopupEngine.register() on DOMContentLoaded.
│ All text, timing and enabled state are configurable via Settings → Popup Manager.
│
│ Templates:
│   1. manual-cta     — Slide-in after scroll/time on /manual (convert readers)
│   2. announcement   — Sticky top bar sitewide for guests (feature highlight)
│   3. exit-register  — Exit intent modal on /manual (registration push)
│   4. help-survey    — Inactivity slide-in on /register (role confusion helper)
--}}
@php
  $ps = \App\Models\Setting::getAllCached();
  // helper: pull setting with fallback
  $p = fn(string $key, mixed $def) => $ps[$key] ?? $def;
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {

  /* ─────────────────────────────────────────────────────────────
   * TEMPLATE 1: MANUAL CTA — Slide-in
   * ───────────────────────────────────────────────────────────── */
  @if(request()->routeIs('manual') && $p('popup_manualcta_enabled', '1') === '1')
  PopupEngine.register('manual-cta', {
    format:        'slideIn',
    cooldown:      {{ (int)$p('popup_manualcta_cooldown_h', 24) * 3600000 }},
    maxTotal:      {{ (int)$p('popup_manualcta_max_total', 3) }},
    maxPerSession: 1,
    bgColor:       '#0f172a',
    textColor:     '#f1f5f9',
    accentColor:   '#f59e0b',
    triggers: [
      { type: 'scrollDepth', percent: {{ (int)$p('popup_manualcta_scroll_pct', 60) }}, delay: 800 },
      { type: 'timeOnPage',  seconds: {{ (int)$p('popup_manualcta_time_s', 45) }} },
    ],
    html: `
      <div style="display:flex;align-items:flex-start;gap:14px;">
        <div style="width:42px;height:42px;border-radius:10px;background:rgba(245,158,11,0.15);
                    border:1px solid rgba(245,158,11,0.3);display:flex;align-items:center;
                    justify-content:center;flex-shrink:0;margin-top:2px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="#f59e0b" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125
                 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013
                 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621
                 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25
                 a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125
                 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504
                 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
          </svg>
        </div>
        <div style="flex:1;min-width:0;">
          <p style="font-family:'Barlow',sans-serif;font-weight:900;font-size:15px;
                    margin:0 0 5px;color:#f1f5f9;line-height:1.2;">
            {{ e($p('popup_manualcta_heading', 'Ready to start tracking?')) }}
          </p>
          <p style="font-size:12px;color:#94a3b8;margin:0 0 14px;line-height:1.5;">
            {{ e($p('popup_manualcta_body', 'Everything in this guide is live. Create a free account and start scoring today.')) }}
          </p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('register') }}"
               class="pu-btn pu-btn--inline"
               style="font-size:13px;padding:9px 16px;">
              Register Free
            </a>
            <a href="{{ route('login') }}"
               class="pu-btn pu-btn--inline"
               style="font-size:13px;padding:9px 16px;background:rgba(255,255,255,0.1);
                      color:#f1f5f9;">
              Sign In
            </a>
          </div>
        </div>
      </div>
    `,
  });
  @endif


  /* ─────────────────────────────────────────────────────────────
   * TEMPLATE 2: ANNOUNCEMENT — Sticky Top Bar
   * ───────────────────────────────────────────────────────────── */
  @guest
  @if($p('popup_announcement_enabled', '1') === '1')
  PopupEngine.register('announcement', {
    format:        'stickyBar',
    position:      'top',
    cooldown:      {{ (int)$p('popup_announcement_cooldown_d', 7) * 86400000 }},
    maxTotal:      {{ (int)$p('popup_announcement_max_total', 4) }},
    maxPerSession: 1,
    bgColor:       '#eff6ff',
    textColor:     '#1e40af',
    accentColor:   '#3b82f6',
    closeOnOverlay: false,
    triggers: [
      { type: 'timeOnPage', seconds: {{ (int)$p('popup_announcement_delay_s', 3) }} },
    ],
    html: `
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0
             001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
      </svg>
      <span style="flex:1;font-size:13px;font-weight:600;line-height:1.4;">
        {{ e($p('popup_announcement_text', 'New: Elimination match scoring (WA Set Point & Compound Cumulative) is now live for club admins.')) }}&nbsp;
        <a href="{{ route('register') }}"
           style="color:#1e40af;font-weight:800;text-decoration:underline;">
          Register free →
        </a>
      </span>
    `,
  });
  @endif
  @endguest


  /* ─────────────────────────────────────────────────────────────
   * TEMPLATE 3: EXIT REGISTER — Exit Intent Modal
   * ───────────────────────────────────────────────────────────── */
  @guest
  @if(request()->routeIs('manual') && $p('popup_exitregister_enabled', '1') === '1')
  PopupEngine.register('exit-register', {
    format:        'modal',
    cooldown:      {{ (int)$p('popup_exitregister_cooldown_h', 48) * 3600000 }},
    maxTotal:      {{ (int)$p('popup_exitregister_max_total', 2) }},
    maxPerSession: 1,
    bgColor:       '#ffffff',
    textColor:     '#0f172a',
    accentColor:   '#f59e0b',
    triggers: [
      { type: 'exitIntent', threshold: 30, delay: 150 },
    ],
    html: `
      <div style="text-align:center;padding-bottom:4px;">
        <div class="pu-icon" style="margin:0 auto 16px;
             background:linear-gradient(135deg,#4338ca,#6366f1);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982
                 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966
                 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>

        <p class="pu-headline" id="pu-title-exit-register">
          {{ e($p('popup_exitregister_heading', 'Before you go…')) }}
        </p>
        <p class="pu-sub" style="max-width:320px;margin-left:auto;margin-right:auto;">
          {{ e($p('popup_exitregister_body', 'Track every score, every end, every session. Free for archers, coaches and clubs.')) }}
        </p>

        <ul class="pu-benefits" style="text-align:left;max-width:300px;margin:0 auto 20px;">
          <li>
            <span class="pu-check">
              <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            WA rounds — indoor, outdoor &amp; field
          </li>
          <li>
            <span class="pu-check">
              <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            Automatic personal best tracking
          </li>
          <li>
            <span class="pu-check">
              <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            Coach assignment &amp; club management
          </li>
          <li>
            <span class="pu-check">
              <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            Elimination match scorecards
          </li>
        </ul>

        <a href="{{ route('register') }}" class="pu-btn">
          Create Free Account
        </a>
        <button class="pu-btn pu-btn--ghost" data-pu-close>
          Maybe later
        </button>
      </div>
    `,
  });
  @endif
  @endguest


  /* ─────────────────────────────────────────────────────────────
   * TEMPLATE 4: HELP SURVEY — Inactivity Slide-in
   * ───────────────────────────────────────────────────────────── */
  @if(request()->routeIs('register') && $p('popup_helpsurvey_enabled', '1') === '1')
  PopupEngine.register('help-survey', {
    format:        'slideIn',
    cooldown:      {{ (int)$p('popup_helpsurvey_cooldown_h', 1) * 3600000 }},
    maxTotal:      {{ (int)$p('popup_helpsurvey_max_total', 2) }},
    maxPerSession: 1,
    bgColor:       '#ffffff',
    textColor:     '#0f172a',
    accentColor:   '#6366f1',
    triggers: [
      { type: 'inactivity', seconds: {{ (int)$p('popup_helpsurvey_inactivity_s', 20) }} },
    ],
    html: `
      <p style="font-family:'Barlow',sans-serif;font-weight:900;font-size:15px;
                margin:0 0 4px;color:#0f172a;line-height:1.2;">
        {{ e($p('popup_helpsurvey_heading', 'Not sure which role to pick?')) }}
      </p>
      <p style="font-size:12px;color:#64748b;margin:0 0 14px;line-height:1.5;">
        {{ e($p('popup_helpsurvey_body', 'Choose the one that fits best — you can always update later.')) }}
      </p>

      <a href="{{ route('register') }}?role=archer"
         class="pu-role-card" style="text-decoration:none;display:flex;">
        <span class="pu-role-emoji">🏹</span>
        <div>
          <p class="pu-role-title">Archer</p>
          <p class="pu-role-desc">Record your own scores and track personal bests.</p>
        </div>
      </a>

      <a href="{{ route('register') }}?role=coach"
         class="pu-role-card" style="text-decoration:none;display:flex;">
        <span class="pu-role-emoji">📋</span>
        <div>
          <p class="pu-role-title">Coach</p>
          <p class="pu-role-desc">Manage a squad of archers and monitor their progress.</p>
        </div>
      </a>

      <a href="{{ route('register') }}?role=club_admin"
         class="pu-role-card" style="text-decoration:none;display:flex;">
        <span class="pu-role-emoji">🏛️</span>
        <div>
          <p class="pu-role-title">Club Admin</p>
          <p class="pu-role-desc">Run a club — manage members, coaches and events.</p>
        </div>
      </a>

      <button class="pu-dismiss" data-pu-close>I'll figure it out myself</button>
    `,
  });
  @endif

});
</script>
