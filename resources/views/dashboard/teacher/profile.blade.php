@extends('layouts.dashboard')

@section('title', 'Profil Saya - TK Ibnul Qoyyim')
@section('page_title', 'Profil Saya')

@section('content')

<x-ui.page-header title="Profil Saya" />

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

@if($errors->any())
    <x-ui.toast variant="danger">
        @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
        @endforeach
    </x-ui.toast>
@endif

@if(!$teacher)
    <x-ui.empty-state message="Akun Anda belum tertaut ke data guru. Hubungi admin." />
@else
    <form method="POST" action="{{ route('admin.teacher.profile.update') }}">
        @csrf
        @method('PUT')

        {{-- Data Akun (read-only) --}}
        <div class="ui-section">
            <h3 class="ui-section__title">Data Akun</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--ui-space-md);">
                <div>
                    <div class="ui-stat-card__label">Email</div>
                    <div>{{ $user->email ?? '-' }}</div>
                </div>
                <div>
                    <div class="ui-stat-card__label">Role</div>
                    <div>Guru</div>
                </div>
                <div>
                    <div class="ui-stat-card__label">Status</div>
                    <div>{{ ucfirst($user->status ?? 'active') }}</div>
                </div>
            </div>
        </div>

        {{-- Data Pribadi --}}
        <div class="ui-section">
            <h3 class="ui-section__title">Data Pribadi</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--ui-space-md);">
                <div class="ui-form-group">
                    <label class="ui-form-label">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Jabatan</label>
                    <input type="text" name="position" value="{{ old('position', $teacher->position) }}" class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $teacher->nip) }}" class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">NUPTK</label>
                    <input type="text" name="nuptk" value="{{ old('nuptk', $teacher->nuptk) }}" class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $teacher->birth_place) }}" class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Tgl Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $teacher->birth_date?->format('Y-m-d')) }}" class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Tgl Mulai Kerja</label>
                    <input type="date" name="start_work_date" value="{{ old('start_work_date', $teacher->start_work_date?->format('Y-m-d')) }}" class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Masa Kerja</label>
                    <input type="text" value="{{ $teacher->masa_kerja ?? '-' }}" disabled class="ui-input" style="background: var(--surface-hover);">
                    <span class="ui-form-hint">Otomatis dari tanggal mulai kerja.</span>
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Pendidikan *</label>
                    <input type="text" name="education" value="{{ old('education', $teacher->education) }}" required class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Telepon *</label>
                    <input type="tel" name="phone_num" value="{{ old('phone_num', $teacher->phone_num) }}" required class="ui-input">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Email Kontak</label>
                    <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="ui-input">
                    <span class="ui-form-hint">Berbeda dari email login (akun).</span>
                </div>
            </div>
        </div>

        {{-- Ubah Password (opsional) --}}
        <div class="ui-section">
            <h3 class="ui-section__title">Ubah Password (opsional)</h3>
            <p class="ui-stat-card__hint" style="margin-bottom: var(--ui-space-md);">
                Kosongkan jika tidak ingin ubah password.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--ui-space-md);">
                <div class="ui-form-group">
                    <label class="ui-form-label">Password Lama</label>
                    <input type="password" name="current_password" class="ui-input" autocomplete="current-password">
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Password Baru</label>
                    <input type="password" name="new_password" class="ui-input" autocomplete="new-password">
                    <span class="ui-form-hint">Min 8 karakter.</span>
                </div>
                <div class="ui-form-group">
                    <label class="ui-form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="ui-input" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div style="display: flex; gap: var(--ui-space-sm); justify-content: flex-end;">
            <button type="submit" class="ui-btn ui-btn--primary">Simpan Perubahan</button>
        </div>
    </form>
@endif

@endsection
