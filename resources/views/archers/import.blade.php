@extends('layouts.app')

@section('title', 'Import Archers')
@section('header', 'Import Archers')
@section('subheader', 'Bulk upload archers from a CSV file')

@section('header-actions')
    <a href="{{ route('archers.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Archers
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @if($errors->any())
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
            <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
            </svg>
            <div>
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    @if(session('import_success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3">
            <p class="text-sm font-bold text-green-700">{{ session('import_success') }}</p>

            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="mt-3 max-h-48 overflow-y-auto rounded-lg bg-white border border-green-200 px-3 py-2 space-y-1">
                    @foreach(session('import_errors') as $err)
                        <p class="text-xs text-amber-700">⚠ {{ $err }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Step 1: Download Template --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-4 flex items-center gap-3" style="background: linear-gradient(135deg,#f0f9ff,#e0f2fe); border-bottom:1px solid #bae6fd;">
            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-black text-white flex-shrink-0"
                  style="background:#0ea5e9;">1</span>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Download the Template</h2>
                <p class="text-xs text-slate-500">Get the CSV template with the correct column headers and an example row</p>
            </div>
        </div>
        <div class="p-6">
            <a href="{{ route('archers.import.template') }}"
               class="inline-flex items-center gap-2 text-sm font-bold px-5 py-2.5 rounded-xl transition-all hover:opacity-90 active:scale-95"
               style="background: linear-gradient(135deg,#0ea5e9,#38bdf8); color:#fff;">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Download archers_template.csv
            </a>

            <div class="mt-5 rounded-xl overflow-hidden" style="border:1px solid #e2e8f0;">
                <table class="w-full text-xs">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th class="text-left px-4 py-2 font-bold text-slate-500 uppercase tracking-wider">Column</th>
                            <th class="text-left px-4 py-2 font-bold text-slate-500 uppercase tracking-wider">Required</th>
                            <th class="text-left px-4 py-2 font-bold text-slate-500 uppercase tracking-wider">Format / Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach([
                            ['name',           'Yes', 'Full name'],
                            ['email',          'Yes', 'Must be unique in the system'],
                            ['nric',           'Yes', '12-digit MyKad number (no dashes)'],
                            ['date_of_birth',  'Yes', 'YYYY-MM-DD  e.g. 2003-04-14'],
                            ['gender',         'Yes', 'male  or  female'],
                            ['place_of_birth', 'Yes', 'City or state'],
                            ['hand',           'Yes', 'right  or  left'],
                            ['classification', 'Yes', 'U12 / U15 / U18 / Open'],
                            ['state',          'Yes', 'e.g. Selangor, Johor, Kuala Lumpur'],
                            ['country',        'Yes', 'e.g. Malaysia'],
                            ['phone',          'No',  'Phone number'],
                            ['club_name',      'No',  'Must match an existing club name exactly'],
                            ['division',       'No',  'Primary: Recurve / Compound / Barebow / Traditional'],
                            ['divisions',      'No',  'All applicable divisions — semicolon-separated e.g. Recurve;Barebow'],
                            ['state_team',     'No',  'e.g. Selangor, Johor, ATM, PDRM, MASUM'],
                            ['national_team',  'No',  'No / Podium / Pelapis Kebangsaan / PARA (default: No)'],
                            ['mareos_id',      'No',  'MAREOS registration ID'],
                            ['wareos_id',      'No',  'WAREOS registration ID'],
                            ['address_line',   'No',  'Street address'],
                            ['postcode',       'No',  '5-digit postcode'],
                            ['address_state',  'No',  'State for the address'],
                            ['notes',          'No',  'Any notes'],
                            ['status',         'No',  'active / no_longer_active / injury (default: active)'],
                            ['para_archery',   'No',  '1 = Para archer, 0 = No (default: 0)'],
                        ] as [$col, $req, $note])
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2 font-mono font-semibold text-indigo-700">{{ $col }}</td>
                            <td class="px-4 py-2">
                                @if($req === 'Yes')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#fee2e2;color:#991b1b;">Required</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#f1f5f9;color:#64748b;">Optional</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-slate-500">{{ $note }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 rounded-xl px-4 py-3 text-xs" style="background:#f0f9ff; border:1px solid #bae6fd;">
                <p class="font-semibold text-sky-700 mb-1">About passwords</p>
                <p class="text-sky-700">Each imported archer gets a randomly generated password. They should use <strong>Forgot Password</strong> on the login page to set their own password using their registered email.</p>
            </div>
        </div>
    </div>

    {{-- Step 2: Fill & Upload --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-4 flex items-center gap-3" style="background: linear-gradient(135deg,#f0fdf4,#dcfce7); border-bottom:1px solid #bbf7d0;">
            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-black text-white flex-shrink-0"
                  style="background:#10b981;">2</span>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Fill in &amp; Upload</h2>
                <p class="text-xs text-slate-500">Open the template in Excel or Google Sheets, add your archers, save as CSV, then upload below</p>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('archers.import.store') }}" enctype="multipart/form-data">
                @csrf

                <div x-data="{ fileName: '' }">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Select CSV File <span class="text-red-500">*</span>
                    </label>

                    <label class="flex flex-col items-center justify-center w-full h-36 rounded-2xl cursor-pointer transition-all"
                           style="border: 2px dashed #cbd5e1; background:#f8fafc;"
                           onmouseover="this.style.borderColor='#6366f1'; this.style.background='#eef2ff';"
                           onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';">
                        <input type="file" name="csv_file" accept=".csv,text/csv" required class="hidden"
                               x-on:change="fileName = $event.target.files[0]?.name ?? ''">
                        <svg class="h-8 w-8 mb-2" style="color:#94a3b8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <p class="text-sm font-semibold text-slate-500" x-text="fileName || 'Click to choose a CSV file'"></p>
                        <p class="text-xs text-slate-400 mt-1">CSV only · max 2MB</p>
                    </label>
                </div>

                <div class="mt-5 rounded-xl px-4 py-3 text-xs" style="background:#fffbeb; border:1px solid #fde68a;">
                    <p class="font-semibold text-amber-700 mb-1">Tips before uploading:</p>
                    <ul class="space-y-0.5 text-amber-700 list-disc list-inside">
                        <li>Keep the first row as column headers (do not rename them)</li>
                        <li>Rows with a duplicate <strong>email</strong> that already has an archer profile will be skipped</li>
                        <li>Use semicolons (;) to separate multiple values in the <strong>divisions</strong> column</li>
                        <li>Dates must be in <strong>YYYY-MM-DD</strong> format (e.g. 2003-04-14)</li>
                        <li>Save your file as <strong>CSV (comma-separated)</strong>, not XLSX</li>
                    </ul>
                </div>

                <div class="flex justify-end mt-5">
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-sm font-black px-6 py-2.5 rounded-xl shadow-md transition-all hover:opacity-90 active:scale-95"
                            style="background: linear-gradient(135deg,#4338ca,#6366f1); color:#fff; font-family:'Barlow',sans-serif;">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        IMPORT ARCHERS
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
