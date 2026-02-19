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
    @if($formMethod === 'PUT')
        @method('PUT')
    @endif

    <div class="space-y-6">

        {{-- Personal Details --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Personal Details</h2>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                @if($archer?->ref_no)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ref No</label>
                        <input type="text" value="{{ $archer->ref_no }}" readonly
                               class="block w-full rounded-md border-gray-200 bg-gray-50 text-sm font-mono
                                      text-gray-500 py-2 px-3 cursor-not-allowed">
                    </div>
                @endif

                <div class="{{ $archer?->ref_no ? '' : 'sm:col-span-2' }}">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $archer?->user?->name) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500
                                  @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $archer?->user?->email) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500
                                  @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth', $archer?->date_of_birth?->format('Y-m-d')) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500
                                  @error('date_of_birth') border-red-400 @enderror">
                    @error('date_of_birth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                @if($archer?->age !== null)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="text" value="{{ $archer->age }} years old" readonly
                               class="block w-full rounded-md border-gray-200 bg-gray-50 text-sm
                                      text-gray-500 py-2 px-3 cursor-not-allowed">
                    </div>
                @endif

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select id="gender" name="gender"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   @error('gender') border-red-400 @enderror">
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender', $archer?->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $archer?->gender) === 'female')>Female</option>
                    </select>
                    @error('gender')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone', $archer?->phone) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="team" class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                    <input type="text" id="team" name="team"
                           value="{{ old('team', $archer?->team) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="club_id" class="block text-sm font-medium text-gray-700 mb-1">Club</label>
                    <select id="club_id" name="club_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                   focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— No Club —</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}"
                                @selected(old('club_id', $archer?->club_id) == $club->id)>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="classification" class="block text-sm font-medium text-gray-700 mb-1">Classification</label>
                    <input type="text" id="classification" name="classification"
                           placeholder="e.g. Bowman, 1st Class"
                           value="{{ old('classification', $archer?->classification) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500">
                </div>

            </div>
        </div>

        {{-- Location --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Location</h2>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="country" name="country"
                           value="{{ old('country', $archer?->country ?? 'Malaysia') }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <select id="state" name="state"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                   focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— Select State —</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}" @selected(old('state', $archer?->state) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="address_line" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea id="address_line" name="address_line" rows="2"
                              class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                     focus:border-indigo-500 focus:ring-indigo-500">{{ old('address_line', $archer?->address_line) }}</textarea>
                </div>

                <div>
                    <label for="postcode" class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                    <input type="text" id="postcode" name="postcode"
                           value="{{ old('postcode', $archer?->postcode) }}"
                           maxlength="10"
                           class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                  focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="address_state" class="block text-sm font-medium text-gray-700 mb-1">Address State</label>
                    <select id="address_state" name="address_state"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                                   focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— Select State —</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}" @selected(old('address_state', $archer?->address_state) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- Archery Details --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Archery Details</h2>

            <fieldset>
                <legend class="text-sm font-medium text-gray-700 mb-3">
                    Division(s) <span class="text-gray-400 text-xs font-normal">(select all that apply)</span>
                </legend>
                <div class="flex flex-wrap gap-5">
                    @foreach($divisions as $div)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="divisions[]"
                                   value="{{ $div }}"
                                   @checked(in_array($div, old('divisions', $archer?->divisions ?? [])))
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">{{ $div }}</span>
                        </label>
                    @endforeach
                </div>
                @error('divisions')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>
        </div>

        {{-- Photo --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Photo</h2>

            <div class="flex items-start gap-6">
                <div class="flex-shrink-0">
                    <img x-show="showPreview" x-cloak :src="photoPreview" alt="Preview"
                         class="h-32 w-28 rounded-lg object-cover border border-gray-200 shadow-sm">
                    <div x-show="!showPreview"
                         class="h-32 w-28 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300
                                flex items-center justify-center text-gray-400 text-xs text-center p-2">
                        No photo
                    </div>
                </div>
                <div class="flex-1">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">
                        Upload Photo
                        <span class="text-gray-400 font-normal text-xs">(BMP, JPG, JPEG, WEBP — max 2MB)</span>
                    </label>
                    <input type="file" id="photo" name="photo"
                           accept=".bmp,.jpg,.jpeg,.webp"
                           @change="handlePhoto($event)"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                  file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100">
                    @error('photo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-400">Passport size photo recommended.</p>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-4">Notes</h2>
            <textarea id="notes" name="notes" rows="3"
                      placeholder="Any additional notes..."
                      class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                             focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $archer?->notes) }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('archers.index') }}"
               class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium
                      text-gray-700 shadow-sm hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white
                           shadow-sm hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-500">
                {{ isset($archer) && $archer->exists ? 'Update Archer' : 'Create Archer' }}
            </button>
        </div>

    </div>
</form>
