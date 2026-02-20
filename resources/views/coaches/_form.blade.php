<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data"
      x-data="{
          photoPreview: '{{ $coach?->photo ? asset('storage/' . $coach->photo) : '' }}',
          showPreview: {{ $coach?->photo ? 'true' : 'false' }},
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
                 style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1);">
                <span class="h-8 w-8 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Personal Details</h2>
                    <p class="text-xs text-gray-500">Basic identity information</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                @if($coach?->ref_no)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Ref No</label>
                        <input type="text" value="{{ $coach->ref_no }}" readonly
                               class="block w-full rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono text-gray-500 py-2.5 px-4 cursor-not-allowed">
                    </div>
                @endif

                <div class="{{ $coach?->ref_no ? '' : 'sm:col-span-2' }}">
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $coach?->user?->name) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition
                                  @error('name') border-red-400 bg-red-50 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Email Address <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $coach?->user?->email) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition
                                  @error('email') border-red-400 bg-red-50 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth', $coach?->date_of_birth?->format('Y-m-d')) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                </div>

                @if($coach?->age !== null)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Age</label>
                        <div class="flex items-center rounded-xl border border-gray-200 bg-gray-50 py-2.5 px-4">
                            <span class="text-sm font-bold text-teal-600">{{ $coach->age }}</span>
                            <span class="text-sm text-gray-500 ml-1">years old</span>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="gender" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Gender</label>
                    <select id="gender" name="gender"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                        <option value="">— Select gender —</option>
                        <option value="male"   @selected(old('gender', $coach?->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $coach?->gender) === 'female')>Female</option>
                    </select>
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Contact Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $coach?->phone) }}"
                           placeholder="e.g. 012-3456789"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                </div>

                <div x-data="{ addNew: {{ old('new_club_name') ? 'true' : 'false' }} }">
                    <label for="club_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Club</label>
                    <select id="club_id" name="club_id" x-show="!addNew"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                        <option value="">— No Club —</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" @selected(old('club_id', $coach?->club_id) == $club->id)>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="new_club_name" x-show="addNew" x-cloak
                           value="{{ old('new_club_name') }}" placeholder="Enter new club name"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                    <button type="button" @click="addNew = !addNew"
                            class="mt-1.5 text-xs font-medium text-teal-600 hover:text-teal-800 hover:underline">
                        <span x-show="!addNew">+ Add new club</span>
                        <span x-show="addNew" x-cloak>← Select existing club</span>
                    </button>
                </div>

                <div>
                    <label for="team" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">State / National Team</label>
                    <input type="text" id="team" name="team" value="{{ old('team', $coach?->team) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="coaching_level" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Coaching Level</label>
                    <select id="coaching_level" name="coaching_level"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                        <option value="">— Select Level —</option>
                        @foreach(\App\Models\Coach::COACHING_LEVELS as $level)
                            <option value="{{ $level }}" @selected(old('coaching_level', $coach?->coaching_level) === $level)>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                    @error('coaching_level')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
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
                                     focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition resize-none">{{ old('address_line', $coach?->address_line) }}</textarea>
                </div>

                <div>
                    <label for="postcode" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Postcode</label>
                    <input type="text" id="postcode" name="postcode" maxlength="10"
                           value="{{ old('postcode', $coach?->postcode) }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="state" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">State</label>
                    <select id="state" name="state"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                        <option value="">— Select State —</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}" @selected(old('state', $coach?->state) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="country" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Country <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="text" id="country" name="country"
                           value="{{ old('country', $coach?->country ?? 'Malaysia') }}"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
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
                        <div x-show="showPreview" x-cloak>
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
                        <label for="photo" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Upload Photo</label>
                        <input type="file" id="photo" name="photo"
                               accept=".bmp,.jpg,.jpeg,.webp"
                               @change="handlePhoto($event)"
                               class="block w-full text-sm text-gray-500
                                      file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700
                                      hover:file:bg-teal-100 file:cursor-pointer file:transition-colors">
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
                             focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition resize-none">{{ old('notes', $coach?->notes) }}</textarea>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('coaches.index') }}"
               class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700
                      hover:bg-gray-50 transition-colors shadow-sm">
                Cancel
            </a>
            <button type="submit"
                    class="px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                    style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                {{ isset($coach) && $coach->exists ? 'Update Coach' : 'Create Coach' }}
            </button>
        </div>

    </div>
</form>
