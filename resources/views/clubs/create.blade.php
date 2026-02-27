@extends('layouts.app')

@section('title', 'New Club')
@section('header', 'New Club')
@section('subheader', 'Create a new club')

@section('header-actions')
    <a href="{{ route('clubs.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('clubs.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                    <h2 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Basic Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Club Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full rounded-xl border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('name') border-red-400 @else border-slate-300 @enderror bg-slate-50">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Registration No.</label>
                            <input type="text" name="registration_number" value="{{ old('registration_number') }}" placeholder="e.g. MAA/2024/001"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Founded Year</label>
                            <input type="number" name="founded_year" value="{{ old('founded_year') }}" min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Club mission, achievements, etc."
                                  class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="active" value="1" {{ old('active', '1') == '1' ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-slate-300 accent-indigo-600">
                            <span class="text-sm text-slate-700">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                    <h2 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Contact</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            @error('contact_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Website</label>
                        <input type="url" name="website" value="{{ old('website') }}" placeholder="https://"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @error('website')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                    <h2 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Location</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Location / City</label>
                        <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Shah Alam, Selangor"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Full Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">State</label>
                        <select name="state" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">— Select state —</option>
                            @foreach(\App\Models\Archer::MALAYSIAN_STATES as $state)
                                <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Logo --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                    <h2 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Logo</h2>
                </div>
                <div class="p-6">
                    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp"
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-slate-400 mt-2">JPG, PNG or WebP · max 2MB</p>
                    @error('logo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('clubs.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-black text-white shadow-md transition-all active:scale-95"
                        style="background: linear-gradient(135deg,#4338ca,#6366f1); font-family:'Barlow',sans-serif;">
                    CREATE CLUB
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
