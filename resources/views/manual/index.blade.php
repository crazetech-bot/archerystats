@extends('layouts.app')

@section('title', 'Archery Stats User Manual — Score Tracking Guide for Archers, Coaches & Clubs | SportDNS')
@section('og_description', 'Complete guide to Archery Stats on SportDNS. Learn how to record archery scores, set up WA rounds, use the custom round builder, manage coaches and clubs, and run elimination matches step by step.')
@section('header', 'Archery Stats User Manual')
@section('subheader', 'Step-by-step guide for archers, coaches and club admins')

@section('content')
@php
    $role = auth()->check() ? (auth()->user()->role ?? 'archer') : 'archer';
    $defaultTab = match($role) {
        'coach'       => 'coach',
        'state_admin' => 'state_admin',
        'club_admin'  => 'club_admin',
        'super_admin' => 'super_admin',
        default       => 'archer',
    };
@endphp

<div class="max-w-4xl mx-auto px-4 py-8 lg:px-6"
     x-data="{ tab: '{{ $defaultTab }}' }">

    {{-- Guest CTA banner --}}
    @guest
    <div class="rounded-2xl overflow-hidden border border-indigo-200 shadow-sm mb-8"
         style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
        <div class="flex flex-col sm:flex-row items-center gap-5 px-6 py-5">
            <div class="flex-shrink-0">
                <span class="h-12 w-12 rounded-2xl flex items-center justify-center"
                      style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
            </div>
            <div class="flex-1 text-center sm:text-left">
                <p class="text-sm font-bold text-indigo-900">Ready to start tracking your scores?</p>
                <p class="text-xs text-indigo-600 mt-0.5">Create a free account to record sessions, view performance charts, and more.</p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90 active:scale-95"
                   style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Register
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all hover:bg-indigo-100"
                   style="color:#4338ca; background:#fff;">
                    Sign In
                </a>
            </div>
        </div>
    </div>
    @endguest

    {{-- Tab bar --}}
    <div class="flex flex-wrap gap-1 p-1 rounded-2xl mb-8" style="background:#f1f5f9;">
        <button @click="tab='archer'"
                :class="tab === 'archer'
                    ? 'bg-white text-indigo-700 shadow-sm font-bold'
                    : 'text-slate-500 hover:text-slate-700 font-semibold'"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm transition-all">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Archer
        </button>
        <button @click="tab='coach'"
                :class="tab === 'coach'
                    ? 'bg-white text-teal-700 shadow-sm font-bold'
                    : 'text-slate-500 hover:text-slate-700 font-semibold'"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm transition-all">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
            </svg>
            Coach
        </button>
        <button @click="tab='club_admin'"
                :class="tab === 'club_admin'
                    ? 'bg-white text-indigo-700 shadow-sm font-bold'
                    : 'text-slate-500 hover:text-slate-700 font-semibold'"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm transition-all">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
            </svg>
            Club Admin
        </button>
        <button @click="tab='state_admin'"
                :class="tab === 'state_admin'
                    ? 'bg-white text-violet-700 shadow-sm font-bold'
                    : 'text-slate-500 hover:text-slate-700 font-semibold'"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm transition-all">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6h1.5m-1.5 3h1.5m-1.5 3h1.5M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
            </svg>
            State Admin
        </button>
        <button @click="tab='super_admin'"
                :class="tab === 'super_admin'
                    ? 'bg-white text-rose-700 shadow-sm font-bold'
                    : 'text-slate-500 hover:text-slate-700 font-semibold'"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm transition-all">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            Super Admin
        </button>
    </div>

    {{-- ─────────────────── ARCHER MANUAL ─────────────────── --}}
    <div x-show="tab === 'archer'" x-cloak>

        <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4" style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                <h2 class="text-white font-black text-lg">Archer — User Guide</h2>
                <p class="text-indigo-200 text-sm mt-0.5">How to use the Archery Stats system as an archer</p>
            </div>
        </div>

        @include('manual._section', [
            'number' => '1',
            'title'  => 'Logging In',
            'color'  => 'indigo',
            'steps'  => [
                'Go to the system URL and enter your email and password.',
                'If you have forgotten your password, click <strong>Forgot Password</strong> and follow the email instructions.',
                'After login you will land on your personal profile page.',
            ],
        ])

        @include('manual._section', [
            'number' => '2',
            'title'  => 'My Profile',
            'color'  => 'indigo',
            'steps'  => [
                'Click <strong>My Profile</strong> in the sidebar to view your archer record.',
                'Your profile shows personal details, equipment, classifications, and personal bests.',
                'To update your information, click the <strong>Edit</strong> button on your profile page.',
                'Upload a photo by clicking the photo area on the edit page.',
            ],
        ])

        @include('manual._section', [
            'number' => '3',
            'title'  => 'Recording a Score (Creating a Session)',
            'color'  => 'indigo',
            'steps'  => [
                'Click <strong>My Sessions</strong> in the sidebar, then click <strong>New Session</strong>.',
                'Select the <strong>Date</strong> of the session.',
                'Choose a <strong>Predefined Round</strong> (e.g. WA 18m Indoor Recurve) from the tabs, or switch to the <strong>Custom</strong> tab to build your own round.',
                'For a custom round: click <strong>Add Ends</strong> to add distance segments. Choose distance, target face size (cm), scoring system, number of ends, and arrows per end for each segment.',
                'Optionally override the <strong>Distance</strong> and <strong>Target Face</strong> for a predefined round using the override card.',
                'Click <strong>Create Session &amp; Start Scoring</strong> to open the scorecard.',
            ],
        ])

        @include('manual._section', [
            'number' => '4',
            'title'  => 'Using the Scorecard',
            'color'  => 'indigo',
            'steps'  => [
                'Each row in the scorecard represents one <strong>end</strong> (a group of arrows).',
                'Click a cell and type the arrow value: <strong>X</strong>, a number (e.g. <strong>9</strong>), or <strong>M</strong> for a miss.',
                'Valid values are shown in the hint above each segment (e.g. <em>X · 10–1 · M</em>).',
                'The end total and running total update automatically after each arrow.',
                'Use the <strong>Previous Set</strong> / <strong>Next Set</strong> buttons to navigate between sets of 6 ends.',
                'Scores are saved automatically when you move between sets. You can also close and return later — your progress is kept.',
                'The summary bar at the top shows Total, X count, 10+X count, Hits, and Misses.',
            ],
        ])

        @include('manual._section', [
            'number' => '5',
            'title'  => 'My Sessions',
            'color'  => 'indigo',
            'steps'  => [
                'Click <strong>My Sessions</strong> in the sidebar to see your full scoring history.',
                'Each session card shows the round name, date, total score, X count, and hit/miss stats.',
                'Click a session to view the full scorecard.',
                'To delete a session, open it and click the <strong>Delete</strong> button (you will be asked to confirm).',
            ],
        ])

        @include('manual._section', [
            'number' => '6',
            'title'  => 'Performance & Arrow Analysis',
            'color'  => 'indigo',
            'steps'  => [
                'Click the <strong>PERFORMANCE</strong> button on your archer profile page to view your progress charts.',
                'Three charts are shown: score trend over time, bar chart of totals by round type, and competition vs training comparison.',
                'Use the date range filter to narrow your analysis period.',
                'The <strong>Arrow Analysis</strong> section at the bottom shows your average score per arrow position (Arrow 1, Arrow 2, Arrow 3, etc.).',
                'Your <strong>weakest arrow</strong> (lowest average) is highlighted in red and your <strong>strongest arrow</strong> is highlighted in green.',
                'Use this chart to identify which arrow position in your end needs the most attention in training.',
            ],
        ])

    </div>

    {{-- ─────────────────── COACH MANUAL ─────────────────── --}}
    <div x-show="tab === 'coach'" x-cloak>

        <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                <h2 class="text-white font-black text-lg">Coach — User Guide</h2>
                <p class="text-teal-100 text-sm mt-0.5">How to use the Archery Stats system as a coach</p>
            </div>
        </div>

        @include('manual._section', [
            'number' => '1',
            'title'  => 'Logging In',
            'color'  => 'teal',
            'steps'  => [
                'Go to the system URL and enter your email and password.',
                'After login you will land on your coach profile page.',
                'The sidebar shows: My Profile, Assigned Archers, Training Sessions, and Club Results.',
            ],
        ])

        @include('manual._section', [
            'number' => '2',
            'title'  => 'My Profile',
            'color'  => 'teal',
            'steps'  => [
                'Click <strong>My Profile</strong> in the sidebar to view your coach record.',
                'Your profile shows personal details, coaching level, club, and notes.',
                'Click <strong>Edit</strong> to update your information or upload a photo.',
            ],
        ])

        @include('manual._section', [
            'number' => '3',
            'title'  => 'Assigned Archers',
            'color'  => 'teal',
            'steps'  => [
                'Click <strong>Assigned Archers</strong> to see a list of archers linked to you.',
                'When a club admin assigns you to an archer, you will receive an invitation by email.',
                'Accept or decline invitations using the link in the email.',
                'Once accepted, click an archer\'s name to view their full profile and scoring history.',
                'You can view the archer\'s performance charts by clicking <strong>Performance</strong> on their profile.',
            ],
        ])

        @include('manual._section', [
            'number' => '4',
            'title'  => 'Training Sessions',
            'color'  => 'teal',
            'steps'  => [
                'Click <strong>Training Sessions</strong> to manage your scheduled training.',
                'Click <strong>New Training Session</strong> to create a training plan.',
                'Fill in the date, time, venue, objectives, and select which assigned archers will attend.',
                'Click a training session to view details or edit it.',
                'Delete a session by opening it and clicking <strong>Delete</strong>.',
            ],
        ])

        @include('manual._section', [
            'number' => '5',
            'title'  => 'Club Results',
            'color'  => 'teal',
            'steps'  => [
                'Click <strong>Club Results</strong> to view scoring sessions for archers in your club.',
                'This is a read-only view — you cannot edit or delete sessions from here.',
                'Click a session to view the full scorecard with end-by-end breakdown.',
            ],
        ])

    </div>

    {{-- ─────────────────── CLUB ADMIN MANUAL ─────────────────── --}}
    <div x-show="tab === 'club_admin'" x-cloak>

        <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4" style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                <h2 class="text-white font-black text-lg">Club Admin — User Guide</h2>
                <p class="text-indigo-200 text-sm mt-0.5">How to manage your club, archers, coaches, and sessions</p>
            </div>
        </div>

        @include('manual._section', [
            'number' => '1',
            'title'  => 'Logging In & Dashboard',
            'color'  => 'indigo',
            'steps'  => [
                'Log in with your club admin email and password.',
                'After login you are redirected to your <strong>Club Dashboard</strong>.',
                'The dashboard shows an overview of your club\'s archers, coaches, and recent activity.',
            ],
        ])

        @include('manual._section', [
            'number' => '2',
            'title'  => 'Managing Archers',
            'color'  => 'indigo',
            'steps'  => [
                'Click <strong>Archers</strong> in the sidebar to see all archers in the system.',
                'Click <strong>New Archer</strong> to register a new archer. Fill in their personal details, divisions, equipment, and classification.',
                'To invite an existing archer to your club, open their profile and use the <strong>Invite to Club</strong> button. They will receive an email to accept.',
                'To remove an archer from your club, go to <strong>My Club → Members</strong> and click <strong>Remove</strong> next to the archer.',
                'To edit an archer\'s profile, open their profile and click <strong>Edit</strong>.',
            ],
        ])

        @include('manual._section', [
            'number' => '3',
            'title'  => 'Managing Coaches',
            'color'  => 'indigo',
            'steps'  => [
                'Click <strong>Coaches</strong> in the sidebar to view all coaches.',
                'Click <strong>New Coach</strong> to register a new coach. Fill in their details and coaching level.',
                'To invite an existing coach to your club, open their profile and use the <strong>Invite to Club</strong> button.',
                'To assign a coach to an archer, go to the archer\'s profile and use the <strong>Assign Coach</strong> section.',
                'To remove a coach from your club, go to <strong>My Club → Members</strong> and click <strong>Remove</strong>.',
            ],
        ])

        @include('manual._section', [
            'number' => '4',
            'title'  => 'My Club',
            'color'  => 'indigo',
            'steps'  => [
                'Click <strong>My Club</strong> in the sidebar to view your club details and member list.',
                'The <strong>Members</strong> tab shows all current archers and coaches in your club.',
                'Pending invitations are listed separately — you can cancel them if needed.',
                'Click <strong>Edit Club</strong> to update your club name, location, and other details.',
            ],
        ])

        @include('manual._section', [
            'number' => '5',
            'title'  => 'Recording Scores',
            'color'  => 'indigo',
            'steps'  => [
                'Open an archer\'s profile, then click <strong>Sessions</strong> and then <strong>New Session</strong>.',
                'Select the session date and choose a predefined round type, or use the <strong>Custom</strong> tab to build a multi-distance round.',
                'For custom rounds: click <strong>Add Ends</strong> to add segments. Set distance, face size, scoring system (e.g. Complete 1–10+X, Reduced 5–10+X), number of ends, and arrows per end.',
                'Click <strong>Create Session &amp; Start Scoring</strong> to open the scorecard.',
                'Enter arrow values (X, 1–10, M). Scores save automatically as you move between sets.',
            ],
        ])

        @include('manual._section', [
            'number' => '6',
            'title'  => 'Scoring Systems Reference',
            'color'  => 'indigo',
            'steps'  => [
                '<strong>Complete (1–10 +X)</strong> — Full 10-zone face. X scores 10 pts. Min valid arrow: 1.',
                '<strong>Reduced (5–10 +X)</strong> — 5-zone face (WA compound). X scores 10 pts. Min valid arrow: 5.',
                '<strong>Reduced (6–10 +X)</strong> — 6-zone face. X scores 10 pts. Min valid arrow: 6.',
                '<strong>Field (1–6)</strong> — Field archery face. X scores 6 pts. Values 1–6 accepted.',
                '<strong>Complete (1–10 =X=11)</strong> — Full face with X counting as 11 pts (tiebreak rounds).',
                '<strong>Reduced (6–10 +X=11)</strong> — 6-zone face with X counting as 11 pts.',
                'The valid range for each scoring system is shown on the scorecard above each segment.',
            ],
        ])

        @include('manual._section', [
            'number' => '7',
            'title'  => 'Elimination Matches',
            'color'  => 'indigo',
            'steps'  => [
                'Click <strong>Elimination Matches</strong> in the sidebar.',
                'Click <strong>New Match</strong> to set up a head-to-head elimination.',
                'Select Archer A and Archer B, choose the match format (WA Set Point or Compound Cumulative), and set the number of ends per set.',
                '<strong>WA Set Point</strong>: each set winner earns 2 match points (tie = 1 each). First to 6 match points wins.',
                '<strong>Compound Cumulative</strong>: total score across all ends decides the winner.',
                'On the scorecard, enter X, 10–5 (or 10–1 for recurve), or M for each arrow. The system calculates set results and match points automatically.',
            ],
        ])

        @include('manual._section', [
            'number' => '8',
            'title'  => 'Live Scoring',
            'color'  => 'indigo',
            'steps'  => [
                'Click the purple <strong>LIVE SCORING</strong> button in the sidebar (above your profile panel) to open the live scoreboard.',
                'The scoreboard shows all archers from your club who have active scoring sessions on the selected date.',
                'Columns displayed: Position, Name, Club, State, Distance subtotals (Dist 1, Dist 2…), Total, 10+X count, X count, and Average per Arrow.',
                'Distance columns are split into 36-arrow blocks — a 72-arrow round shows two distance columns, a 144-arrow round shows four, and so on.',
                'Archers are ranked by total score, with X count and 10+X count used as tiebreakers.',
                'The scoreboard auto-refreshes at the selected interval — choose between 15 seconds and 5 minutes using the <strong>Refresh</strong> dropdown.',
                'Use the <strong>Date</strong> picker at the top to view a different day\'s scoreboard.',
            ],
        ])

    </div>

    {{-- ─────────────────── STATE ADMIN MANUAL ─────────────────── --}}
    <div x-show="tab === 'state_admin'" x-cloak>

        <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                <h2 class="text-white font-black text-lg">State Admin — User Guide</h2>
                <p class="text-violet-200 text-sm mt-0.5">Managing your state team as a state team administrator</p>
            </div>
        </div>

        @include('manual._section', [
            'number' => '1',
            'title'  => 'Logging In',
            'color'  => 'violet',
            'steps'  => [
                'Log in with your state admin email and password.',
                'After login you are redirected directly to your <strong>State Team Profile</strong> page.',
                'You can only view and manage the state team you have been assigned to — other state teams are not accessible.',
                'The sidebar shows: Archers, Coaches, <strong>Clubs</strong> (read-only), State Teams (your team only), and Settings.',
            ],
        ])

        @include('manual._section', [
            'number' => '2',
            'title'  => 'My State Team Profile',
            'color'  => 'violet',
            'steps'  => [
                'Your state team profile shows the team name, state, registration number, contact details, and current admin.',
                'The <strong>Archers</strong> tab lists all archers assigned to your state team.',
                'The <strong>Coaches</strong> tab lists all coaches assigned to your state team.',
                'Click <strong>Edit</strong> to update your team\'s contact details, description, logo, or status.',
                'Only archers and coaches whose records have your state team selected will appear in these lists.',
            ],
        ])

        @include('manual._section', [
            'number' => '3',
            'title'  => 'Appointing a Team Admin',
            'color'  => 'violet',
            'steps'  => [
                'On your State Team Profile page, scroll to the <strong>Appoint Team Admin</strong> section.',
                'The dropdown lists all coaches in the system who are eligible to be appointed.',
                'Select the coach you wish to appoint and click <strong>Confirm Appointment</strong>.',
                'The appointed coach will be promoted to <strong>State Admin</strong> role and will retain their coaching privileges.',
                'The newly appointed admin will appear in the <strong>Team Admin</strong> section at the top of the profile page.',
                'Only one admin can be assigned per team. Appointing a new admin replaces the previous record.',
            ],
        ])

        @include('manual._section', [
            'number' => '4',
            'title'  => 'Coach Privileges (Dual Role)',
            'color'  => 'violet',
            'steps'  => [
                'If you were appointed as state admin from a coach role, you retain all coach-level access.',
                'You will still appear in the Coaches list and can access coach-specific features (assigned archers, training sessions, club results).',
                'Your role shows as <strong>State Admin</strong> but the system recognises your coaching status automatically.',
                'This dual-role behaviour means you do not lose your existing work or linked archers when promoted.',
            ],
        ])

        @include('manual._section', [
            'number' => '5',
            'title'  => 'Viewing Clubs',
            'color'  => 'violet',
            'steps'  => [
                'Click <strong>Clubs</strong> in the sidebar to browse all registered clubs.',
                'Click a club name to view its profile, including member archers and coaches.',
                'This is a read-only view — state admins cannot create or delete clubs.',
                'Use this to check which club an archer or coach belongs to.',
            ],
        ])

        @include('manual._section', [
            'number' => '6',
            'title'  => 'Live Scoring',
            'color'  => 'violet',
            'steps'  => [
                'Click the purple <strong>LIVE SCORING</strong> button in the sidebar to open the live scoreboard.',
                'The scoreboard shows all archers assigned to your state team who have active sessions on the selected date.',
                'Columns displayed: Position, Name, Club, State, Distance subtotals, Total, 10+X, X, and Average per Arrow.',
                'Archers are ranked by total score, with X count and 10+X count used as tiebreakers.',
                'The scoreboard auto-refreshes at the selected interval (15 seconds to 5 minutes).',
                'Use the <strong>Date</strong> picker to view a different day\'s scoreboard.',
            ],
        ])

    </div>

    {{-- ─────────────────── SUPER ADMIN MANUAL ─────────────────── --}}
    <div x-show="tab === 'super_admin'" x-cloak>

        <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4" style="background: linear-gradient(135deg, #e11d48, #f43f5e);">
                <h2 class="text-white font-black text-lg">Super Admin — User Guide</h2>
                <p class="text-rose-200 text-sm mt-0.5">Full system administration — users, roles, clubs, state teams</p>
            </div>
        </div>

        @include('manual._section', [
            'number' => '1',
            'title'  => 'Logging In & Dashboard',
            'color'  => 'rose',
            'steps'  => [
                'Log in with your super admin email and password.',
                'After login you are redirected to the <strong>Archers</strong> index — full system access is available from the sidebar.',
                'You can access all modules: Archers, Coaches, Clubs, State Teams, Sessions, and Settings.',
            ],
        ])

        @include('manual._section', [
            'number' => '2',
            'title'  => 'New User Registration Notifications',
            'color'  => 'rose',
            'steps'  => [
                'Every time a new user registers an account, <strong>all super admins receive an email notification</strong>.',
                'The email includes the new user\'s name, email address, role, club (if selected), and registration timestamp.',
                'A link to the Admin → Settings page is included for quick access to manage the new user.',
                'No configuration is required — notifications are sent automatically on every registration.',
            ],
        ])

        @include('manual._section', [
            'number' => '3',
            'title'  => 'Managing Users (Settings → Users)',
            'color'  => 'rose',
            'steps'  => [
                'Go to <strong>Settings</strong> in the sidebar, then click the <strong>Users</strong> tab.',
                'The Users table lists every registered user with their role, email, and status.',
                'Use the <strong>Suspend</strong> button to prevent a user from logging in. Suspended users see an error message at login.',
                'Use the <strong>Unsuspend</strong> button to restore access.',
                'Click <strong>Delete</strong> to permanently remove a user account (requires confirmation).',
            ],
        ])

        @include('manual._section', [
            'number' => '4',
            'title'  => 'Appointing Club Admin or State Admin',
            'color'  => 'rose',
            'steps'  => [
                'In Settings → Users, find the user you want to promote and click <strong>Appoint</strong>.',
                'Select the target role: <strong>Club Admin</strong> or <strong>State Admin</strong>.',
                'If promoting to Club Admin, also select the club they will manage.',
                'Click <strong>Confirm</strong> to apply the role change.',
                'If the user was previously a coach, they <strong>retain their coaching privileges</strong> — they remain visible in the Coaches list and keep access to coach features.',
                'Only archers and coaches can be appointed to admin roles. Super admins cannot be demoted from this interface.',
            ],
        ])

        @include('manual._section', [
            'number' => '5',
            'title'  => 'Managing State Teams',
            'color'  => 'rose',
            'steps'  => [
                'Click <strong>State Teams</strong> in the sidebar to view all state teams.',
                'Click <strong>New State Team</strong> to create a team. Fill in the name, state, registration number, contact details, and optionally upload a logo.',
                'Click a state team to open its profile. The profile shows all assigned archers, coaches, and the current admin.',
                'To appoint a state team admin, scroll to the <strong>Appoint Team Admin</strong> section and select a coach from the dropdown.',
                'The appointed coach is promoted to <strong>State Admin</strong> role. They can then manage the team and appoint future admins themselves.',
                'To delete a state team, open it and click the <strong>Delete</strong> button (super admin only).',
            ],
        ])

        @include('manual._section', [
            'number' => '6',
            'title'  => 'Managing Clubs',
            'color'  => 'rose',
            'steps'  => [
                'Click <strong>Clubs</strong> in the sidebar to view all clubs.',
                'Click <strong>New Club</strong> to create a club. Enter the name, state, and contact details.',
                'To import multiple clubs at once, click <strong>Import CSV</strong> and upload a CSV file with columns: name, state.',
                'Click a club to view its member archers and coaches.',
                'To edit a club, open it and click <strong>Edit</strong>. To delete, click <strong>Delete</strong> (permanent).',
            ],
        ])

        @include('manual._section', [
            'number' => '7',
            'title'  => 'Managing Archers & Coaches',
            'color'  => 'rose',
            'steps'  => [
                'Click <strong>Archers</strong> to view all archers system-wide. You can create, edit, and delete any archer.',
                'Click <strong>Coaches</strong> to view all coaches. You can create, edit, and delete any coach.',
                'Open an archer\'s profile to view their sessions, classification, personal bests, and equipment.',
                'Use the <strong>New Session</strong> button on an archer\'s profile to record a scoring session on their behalf.',
                'To delete an archer or coach, open their profile and click <strong>Delete</strong> (requires confirmation).',
            ],
        ])

        @include('manual._section', [
            'number' => '8',
            'title'  => 'Scoring Systems Reference',
            'color'  => 'rose',
            'steps'  => [
                '<strong>Complete (1–10 +X)</strong> — Full 10-zone face. X scores 10 pts. Min valid arrow: 1.',
                '<strong>Reduced (5–10 +X)</strong> — 5-zone face (WA compound). X scores 10 pts. Min valid arrow: 5.',
                '<strong>Reduced (6–10 +X)</strong> — 6-zone face. X scores 10 pts. Min valid arrow: 6.',
                '<strong>Field (1–6)</strong> — Field archery face. X scores 6 pts. Values 1–6 accepted.',
                '<strong>Complete (1–10 =X=11)</strong> — Full face with X counting as 11 pts (tiebreak rounds).',
                '<strong>Reduced (6–10 +X=11)</strong> — 6-zone face with X counting as 11 pts.',
            ],
        ])

        @include('manual._section', [
            'number' => '9',
            'title'  => 'Live Scoring',
            'color'  => 'rose',
            'steps'  => [
                'Click the purple <strong>LIVE SCORING</strong> button in the sidebar to open the live scoreboard.',
                'Super admins see all archers system-wide. Use the filter bar to narrow the view.',
                'Use the <strong>Filter by Club</strong> dropdown to view only archers from a specific club.',
                'Use the <strong>Filter by State Team</strong> dropdown to narrow to a state team.',
                'Use the <strong>Filter by National Team</strong> dropdown to view Podium, Pelapis Kebangsaan, or PARA archers.',
                'Filters can be combined freely. Clear a filter by selecting the blank option.',
                'The scoreboard auto-refreshes at the selected interval. Use the <strong>Date</strong> picker to view any day\'s scoreboard.',
                'Distance columns are split into 36-arrow blocks — a 72-arrow round shows two columns, a 144-arrow round shows four.',
            ],
        ])

    </div>

    {{-- Guest bottom CTA --}}
    @guest
    <div class="mt-8 rounded-2xl border border-indigo-100 bg-white shadow-sm px-6 py-6 text-center">
        <p class="text-base font-bold text-gray-800 mb-1">Looks good? Get started for free.</p>
        <p class="text-sm text-gray-500 mb-5">Register as an archer, coach, or club admin to access the full system.</p>
        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90"
               style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Create Account
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 text-gray-600 hover:border-indigo-300 hover:text-indigo-700 transition-all">
                Already have an account? Sign In
            </a>
        </div>
    </div>
    @endguest

</div>
@endsection
