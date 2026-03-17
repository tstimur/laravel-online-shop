@php($selectedRoles = old('roles', $selectedRoles ?? []))

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="first_name" class="form-label">First name</label>
                   <input type="text"
                           name="first_name"
                           id="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $user?->first_name ?? '') }}"
                           required>
                    @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label for="last_name" class="form-label">Last name</label>
                   <input type="text"
                           name="last_name"
                           id="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $user?->last_name ?? '') }}"
                           required>
                    @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mt-0">
                <div class="col-12 col-md-6">
                    <label for="email" class="form-label">Email</label>
                   <input type="email"
                           name="email"
                           id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user?->email ?? '') }}"
                           required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                   <input type="text"
                           name="phone"
                           id="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $user?->phone ?? '') }}">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @if($showPassword)
                <div class="row g-3 mt-0">
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm password</label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               required>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-4">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status"
                        id="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required>
                    @foreach(\App\Models\User::STATUSES as $status)
                        <option value="{{ $status }}"
                            @selected(old('status', $user?->status ?? \App\Models\User::STATUS_ACTIVE) === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="roles" class="form-label">Roles</label>
                <select name="roles[]"
                        id="roles"
                        class="form-select @error('roles') is-invalid @enderror"
                        multiple>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            @selected(in_array($role->id, $selectedRoles, true))>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('roles')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar</label>
                <input type="file"
                       name="avatar"
                       id="avatar"
                       class="form-control @error('avatar') is-invalid @enderror"
                       accept="image/*">
                @error('avatar')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if(!empty($user?->avatar))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->full_name }}"
                             class="img-fluid rounded"
                             style="max-height: 140px;">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
