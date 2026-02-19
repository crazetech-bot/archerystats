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
                    <label for="phone" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Phone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $archer?->phone) }}"
                           placeholder="e.g. 012-3456789"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label for="team" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Team</label>
                    <input type="text" id="team" name="team" value="{{ old('team', $archer?->team) }}"
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

                <div>
                    <label for="country" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Country <span class="text-red-500 normal-case font-normal">*</span>
                    </label>
                    <input type="text" id="country" name="country"
                           value="{{ old('country', $archer?->country ?? 'Malaysia') }}"
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
                    <label for="address_state" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Address State</label>
                    <select id="address_state" name="address_state"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        <option value="">— Select State —</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}" @selected(old('address_state', $archer?->address_state) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- Archery Details --}}
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
                    <p class="text-xs text-gray-500">Select all applicable divisions</p>
                </div>
            </div>
            <div class="p-6">
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
