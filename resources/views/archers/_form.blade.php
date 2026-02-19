<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data"
      x-data="{
          photoPreview: '{{ $archer?->photo ? asset('storage/' . $archer->photo) : '' }}',
          showPreview: {{ $archer?->photo ? 'true' : 'false' }},
          handlePhoto(e) {
              const file = e.target.files[0];
              if (file) {
                  this.photoPreview = URL.createObjectURL(file);
                  this.showPreview = true;
              }
          }
      }">
    @csrf
    @if($formMethod === 'PUT') @method('PUT') @endif

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Personal Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #f8faff, #f0f4ff);">
                <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Personal Details</h2>
                    <p class="text-xs text-gray-500">Basic identity information</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                @if($archer?->ref_no)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Ref No</label>
                        <input type="text" value="{{ $archer->ref_no }}" readonly
                               class="block w-full rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono text-gray-500 py-2.5 px-4 cursor-not-allowed">
                    </div>
                @endif

                <div class="{{ $archer?->ref_no ? '' : 'sm:col-span-2' }}">
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $archer?->user?->name) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                  @error('name') border-red-400 bg-red-50 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Email Address <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $archer?->user?->email) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                  @error('email') border-red-400 bg-red-50 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Date of Birth <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth', $archer?->date_of_birth?->format('Y-m-d')) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                  @error('date_of_birth') border-red-400 bg-red-50 @enderror">
                    @error('date_of_birth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                @if($archer?->age !== null)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Age</label>
                        <div class="flex items-center rounded-xl border border-gray-200 bg-gray-50 py-2.5 px-4">
                            <span class="text-sm font-bold text-indigo-600">{{ $archer->age }}</span>
                            <span class="text-sm text-gray-500 ml-1">years old</span>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="gender" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Gender <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <select id="gender" name="gender"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                   @error('gender') border-red-400 @enderror">
                        <option value="">Select gender</option>
                        <option value="male"   @selected(old('gender', $archer?->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $archer?->gender) === 'female')>Female</option>
                    </select>
                    @error('gender')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Contact Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $archer?->phone) }}"
                           placeholder="e.g. 012-3456789"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

                <div x-data="{ addNew: {{ old('new_club_name') ? 'true' : 'false' }} }">
                    <label for="club_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Club</label>
                    <select id="club_id" name="club_id" x-show="!addNew"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        <option value="">— No Club —</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" @selected(old('club_id', $archer?->club_id) == $club->id)>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="new_club_name" x-show="addNew" x-cloak
                           value="{{ old('new_club_name') }}" placeholder="Enter new club name"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    <button type="button" @click="addNew = !addNew"
                            class="mt-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                        <span x-show="!addNew">+ Add new club</span>
                        <span x-show="addNew" x-cloak>← Select existing club</span>
                    </button>
                </div>

                <div>
                    <label for="team" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">State / National Team</label>
                    <input type="text" id="team" name="team" value="{{ old('team', $archer?->team) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="hand" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">RH / LH</label>
                    <select id="hand" name="hand"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                   @error('hand') border-red-400 @enderror">
                        <option value="">— Select —</option>
                        <option value="right" @selected(old('hand', $archer?->hand) === 'right')>Right Handed</option>
                        <option value="left"  @selected(old('hand', $archer?->hand) === 'left')>Left Handed</option>
                    </select>
                    @error('hand')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Location --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Location</h2>
                    <p class="text-xs text-gray-500">Address and region details</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="address_line" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Address</label>
                    <textarea id="address_line" name="address_line" rows="2"
                              class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                     focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition resize-none">{{ old('address_line', $archer?->address_line) }}</textarea>
                </div>

                <div>
                    <label for="postcode" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Postcode</label>
                    <input type="text" id="postcode" name="postcode" maxlength="10"
                           value="{{ old('postcode', $archer?->postcode) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="state" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">State</label>
                    <select id="state" name="state"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        <option value="">— Select State —</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}" @selected(old('state', $archer?->state) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="country" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Country <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="text" id="country" name="country"
                           value="{{ old('country', $archer?->country ?? 'Malaysia') }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

            </div>
        </div>

        {{-- Equipment --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #fff7ed, #ffedd5);">
                <span class="h-8 w-8 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l5.654-4.654m5.896-2.572c.083-.283.27-.576.604-.818L21 8.25l-4.5-4.5-2.053 2.053c-.242.334-.535.52-.818.604m-5.585 5.585L3 21"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Equipment</h2>
                    <p class="text-xs text-gray-500">Arrow and limb setup</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <label for="arrow_type" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Arrow Type</label>
                    <input type="text" id="arrow_type" name="arrow_type"
                           value="{{ old('arrow_type', $archer?->arrow_type) }}"
                           placeholder="e.g. X10, ACE, Carbon Express"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="arrow_size" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Arrow Size</label>
                    <input type="text" id="arrow_size" name="arrow_size"
                           value="{{ old('arrow_size', $archer?->arrow_size) }}"
                           placeholder="e.g. 500, 1714"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="arrow_length" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Arrow Length (inches)</label>
                    <input type="number" id="arrow_length" name="arrow_length" step="0.1" min="0" max="999"
                           value="{{ old('arrow_length', $archer?->arrow_length) }}"
                           placeholder="e.g. 29.5"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    @error('arrow_length')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="limb_type" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Limb Type</label>
                    <input type="text" id="limb_type" name="limb_type"
                           value="{{ old('limb_type', $archer?->limb_type) }}"
                           placeholder="e.g. SF Premium, Samick"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="limb_length" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Limb Length (inches)</label>
                    <input type="number" id="limb_length" name="limb_length" step="0.5" min="0" max="999"
                           value="{{ old('limb_length', $archer?->limb_length) }}"
                           placeholder="e.g. 68"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    @error('limb_length')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="limb_poundage" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Limb Poundage (lbs)</label>
                    <input type="number" id="limb_poundage" name="limb_poundage" step="0.5" min="0" max="999"
                           value="{{ old('limb_poundage', $archer?->limb_poundage) }}"
                           placeholder="e.g. 38.5"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    @error('limb_poundage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="actual_poundage" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Actual Poundage (lbs)</label>
                    <input type="number" id="actual_poundage" name="actual_poundage" step="0.5" min="0" max="999"
                           value="{{ old('actual_poundage', $archer?->actual_poundage) }}"
                           placeholder="e.g. 36.0"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    @error('actual_poundage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Archery Details: Division + Classification --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #fffbeb, #fef3c7);">
                <span class="h-8 w-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 6a4 4 0 100 8 4 4 0 000-8zm0 2a2 2 0 110 4 2 2 0 010-4z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Archery Details</h2>
                    <p class="text-xs text-gray-500">Division, classification and category</p>
                </div>
            </div>
            <div class="p-6 space-y-6">

                {{-- Division --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Division <span class="text-gray-400 font-normal normal-case">(select all that apply)</span></p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($divisions as $div)
                            @php $checked = in_array($div, old('divisions', $archer?->divisions ?? [])); @endphp
                            <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                          {{ $checked ? 'border-amber-400 bg-amber-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}"
                                   x-data="{ checked: {{ $checked ? 'true' : 'false' }} }"
                                   :class="checked ? 'border-amber-400 bg-amber-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300'">
                                <input type="checkbox" name="divisions[]" value="{{ $div }}"
                                       @checked($checked)
                                       x-model="checked"
                                       class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                                <span class="text-sm font-semibold" :class="checked ? 'text-amber-700' : 'text-gray-600'">{{ $div }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('divisions')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100"></div>

                {{-- Classification --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Classification <span class="text-gray-400 font-normal normal-case">(choose one)</span></p>
                    @php
                        $currentClass = old('classification', $archer?->classification);
                        $clsOptions = [
                            'U12'  => ['label' => 'Under 12',   'border' => '#38bdf8', 'bg' => '#f0f9ff', 'color' => '#0369a1'],
                            'U15'  => ['label' => 'Under 15',   'border' => '#a78bfa', 'bg' => '#f5f3ff', 'color' => '#6d28d9'],
                            'U18'  => ['label' => 'Under 18',   'border' => '#fb7185', 'bg' => '#fff1f2', 'color' => '#be123c'],
                            'Open' => ['label' => 'Open Class', 'border' => '#34d399', 'bg' => '#ecfdf5', 'color' => '#065f46'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3"
                         x-data="{ selected: '{{ $currentClass ?? '' }}' }">
                        @foreach($clsOptions as $cls => $opt)
                            <label
                                :style="selected === '{{ $cls }}'
                                    ? 'border-color: {{ $opt['border'] }}; background-color: {{ $opt['bg'] }};'
                                    : 'border-color: #e5e7eb; background-color: #f9fafb;'"
                                class="flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 cursor-pointer transition-all">
                                <input type="radio" name="classification" value="{{ $cls }}"
                                       x-model="selected"
                                       class="sr-only">
                                <span class="text-lg font-black transition-colors"
                                      :style="selected === '{{ $cls }}' ? 'color: {{ $opt['color'] }}' : 'color: #9ca3af'">{{ $cls }}</span>
                                <span class="text-xs transition-colors"
                                      :style="selected === '{{ $cls }}' ? 'color: {{ $opt['color'] }}' : 'color: #9ca3af'">{{ $opt['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('classification')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Personal Best (Unofficial — Training) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe);">
                <span class="h-8 w-8 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Personal Best <span class="font-normal text-gray-500">&mdash; Unofficial (Training)</span></h2>
                    <p class="text-xs text-gray-500">Best scores recorded during training sessions</p>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- 36 Arrows --}}
                    <div class="rounded-2xl border border-sky-100 bg-sky-50/40 p-5 space-y-4">
                        <p class="text-xs font-bold text-sky-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="inline-flex h-5 w-5 rounded-full bg-sky-200 text-sky-800 text-xs font-black items-center justify-center">36</span>
                            Arrows
                        </p>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Score</label>
                            <input type="number" name="pb_unofficial_36_score" min="0" max="9999"
                                   value="{{ old('pb_unofficial_36_score', $archer?->pb_unofficial_36_score) }}"
                                   placeholder="e.g. 320"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date</label>
                            <input type="date" name="pb_unofficial_36_date"
                                   value="{{ old('pb_unofficial_36_date', $archer?->pb_unofficial_36_date?->format('Y-m-d')) }}"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition">
                        </div>
                    </div>

                    {{-- 72 Arrows --}}
                    <div class="rounded-2xl border border-sky-100 bg-sky-50/40 p-5 space-y-4">
                        <p class="text-xs font-bold text-sky-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="inline-flex h-5 w-5 rounded-full bg-sky-200 text-sky-800 text-xs font-black items-center justify-center">72</span>
                            Arrows
                        </p>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Score</label>
                            <input type="number" name="pb_unofficial_72_score" min="0" max="9999"
                                   value="{{ old('pb_unofficial_72_score', $archer?->pb_unofficial_72_score) }}"
                                   placeholder="e.g. 640"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date</label>
                            <input type="date" name="pb_unofficial_72_date"
                                   value="{{ old('pb_unofficial_72_date', $archer?->pb_unofficial_72_date?->format('Y-m-d')) }}"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Personal Best (Official) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Personal Best <span class="font-normal text-gray-500">&mdash; Official</span></h2>
                    <p class="text-xs text-gray-500">Best scores recorded at official tournaments</p>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Official 36 Arrows --}}
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5 space-y-4">
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="inline-flex h-5 w-5 rounded-full bg-emerald-200 text-emerald-800 text-xs font-black items-center justify-center">36</span>
                            Arrows
                        </p>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Score</label>
                            <input type="number" name="pb_official_36_score" min="0" max="9999"
                                   value="{{ old('pb_official_36_score', $archer?->pb_official_36_score) }}"
                                   placeholder="e.g. 340"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date</label>
                            <input type="date" name="pb_official_36_date"
                                   value="{{ old('pb_official_36_date', $archer?->pb_official_36_date?->format('Y-m-d')) }}"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tournament</label>
                            <input type="text" name="pb_official_36_tournament"
                                   value="{{ old('pb_official_36_tournament', $archer?->pb_official_36_tournament) }}"
                                   placeholder="e.g. National Open 2024"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                    </div>

                    {{-- Official 72 Arrows --}}
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5 space-y-4">
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="inline-flex h-5 w-5 rounded-full bg-emerald-200 text-emerald-800 text-xs font-black items-center justify-center">72</span>
                            Arrows
                        </p>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Score</label>
                            <input type="number" name="pb_official_72_score" min="0" max="9999"
                                   value="{{ old('pb_official_72_score', $archer?->pb_official_72_score) }}"
                                   placeholder="e.g. 660"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date</label>
                            <input type="date" name="pb_official_72_date"
                                   value="{{ old('pb_official_72_date', $archer?->pb_official_72_date?->format('Y-m-d')) }}"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tournament</label>
                            <input type="text" name="pb_official_72_tournament"
                                   value="{{ old('pb_official_72_tournament', $archer?->pb_official_72_tournament) }}"
                                   placeholder="e.g. National Open 2024"
                                   class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Photo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #fdf4ff, #fae8ff);">
                <span class="h-8 w-8 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Photo</h2>
                    <p class="text-xs text-gray-500">Passport size photo</p>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0">
                        <div x-show="showPreview" x-cloak class="relative">
                            <img :src="photoPreview" alt="Preview"
                                 class="h-36 w-28 rounded-2xl object-cover border-2 border-gray-200 shadow-md">
                        </div>
                        <div x-show="!showPreview"
                             class="h-36 w-28 rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300
                                    flex flex-col items-center justify-center text-gray-400 gap-2">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                            <span class="text-xs">No photo</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <label for="photo" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Upload Photo
                        </label>
                        <input type="file" id="photo" name="photo"
                               accept=".bmp,.jpg,.jpeg,.webp"
                               @change="handlePhoto($event)"
                               class="block w-full text-sm text-gray-500
                                      file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100 file:cursor-pointer file:transition-colors">
                        @error('photo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-2 text-xs text-gray-400">BMP, JPG, JPEG or WEBP &mdash; max 2MB. Passport size recommended.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label for="notes" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Any additional notes..."
                      class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                             focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition resize-none">{{ old('notes', $archer?->notes) }}</textarea>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('archers.index') }}"
               class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700
                      hover:bg-gray-50 transition-colors shadow-sm">
                Cancel
            </a>
            <button type="submit"
                    class="px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                    style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                {{ isset($archer) && $archer->exists ? 'Update Archer' : 'Create Archer' }}
            </button>
        </div>

    </div>
</form>
