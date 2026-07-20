@extends('layouts.app')

@section('title', 'Kelola Akun Saya')
@section('header_title', 'Kelola Akun Saya')
@section('header_subtitle', 'Perbarui informasi alamat email dan kata sandi akses akun Anda')

@section('content')
<div class="card" style="max-width: 680px; margin: 0 auto; position: relative; overflow: hidden;">
    <div style="position: absolute; width: 180px; height: 180px; background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%); top: -60px; right: -60px; pointer-events: none;"></div>
    
    <div class="section-header" style="margin-bottom: 24px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
        <h3 class="section-title" style="margin: 0;">
            <i class="bi bi-person-gear text-accent" style="color: var(--color-accent);"></i>
            Pengaturan Akun: {{ $user->name }}
        </h3>
        <span class="badge {{ $user->role === 'admin' ? 'badge-warning' : 'badge-info' }}" style="padding: 6px 12px; font-size: 11px;">
            <i class="bi {{ $user->role === 'admin' ? 'bi-shield-lock-fill' : 'bi-person-badge-fill' }}"></i> {{ ucfirst($user->role) }}
        </span>
    </div>

    <!-- Informational Alert -->
    <div style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: var(--radius-sm); padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
        <i class="bi bi-info-circle-fill text-primary" style="font-size: 18px; color: var(--color-primary); margin-top: 2px;"></i>
        <div style="font-size: 12px; color: var(--text-primary); line-height: 1.5;">
            <strong>Pengaturan Informasi Akun Pengguna</strong>
            <p style="margin: 2px 0 0 0; color: var(--text-muted);">
                Anda dapat mengubah Alamat Email dan Kata Sandi (Password) Anda secara mandiri. Nama akun dan hak akses role dikelola oleh Administrator.
            </p>
        </div>
    </div>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Nama Pengguna (Read-only) -->
        <div class="form-group" style="margin-bottom: 18px;">
            <label style="font-size: 12px; font-weight: 600; color: var(--text-muted);">Nama Lengkap <span style="font-weight: normal; font-size: 11px;">(Terkunci)</span></label>
            <div style="position: relative;">
                <input type="text" class="form-control" value="{{ $user->name }}" disabled readonly style="padding-left: 45px; background: rgba(255,255,255,0.02); opacity: 0.75; cursor: not-allowed;">
                <i class="bi bi-lock-fill" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px;"></i>
            </div>
            <span style="font-size: 10px; color: var(--text-muted); display: block; margin-top: 4px;">Perubahan nama akun hanya dapat dilakukan melalui Administrator.</span>
        </div>

        <!-- Alamat Email (Editable) -->
        <div class="form-group" style="margin-bottom: 18px;">
            <label for="email" style="font-size: 12px; font-weight: 600; color: var(--text-primary);">Alamat Email <span class="text-danger" style="color: var(--color-danger);">*</span></label>
            <div style="position: relative;">
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@detpak.co.id" value="{{ old('email', $user->email) }}" required style="padding-left: 45px;">
                <i class="bi bi-envelope-fill" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--color-primary); font-size: 15px;"></i>
            </div>
            @error('email')
                <p class="error-text" style="color: var(--color-danger); font-size: 11px; margin-top: 4px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="border-top: 1px dashed var(--glass-border); margin: 20px 0; padding-top: 16px;">
            <h4 style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-shield-lock text-warning" style="color: var(--color-warning);"></i> Ubah Kata Sandi (Password)
            </h4>

            <!-- Kata Sandi Baru -->
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="password" style="font-size: 12px; font-weight: 500; color: var(--text-primary);">
                    Kata Sandi Baru 
                    <span style="font-weight: normal; font-size: 11px; color: var(--text-muted);">(Biarkan kosong jika tidak ingin mengganti)</span>
                </label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter..." style="padding-left: 45px;">
                    <i class="bi bi-key-fill" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px;"></i>
                </div>
                @error('password')
                    <p class="error-text" style="color: var(--color-danger); font-size: 11px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Kata Sandi Baru -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password_confirmation" style="font-size: 12px; font-weight: 500; color: var(--text-primary);">Konfirmasi Kata Sandi Baru</label>
                <div style="position: relative;">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru..." style="padding-left: 45px;">
                    <i class="bi bi-check2-circle" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px;"></i>
                </div>
            </div>
        </div>

        <!-- Submit & Action Buttons -->
        <div class="form-actions" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; border-top: 1px solid var(--glass-border); padding-top: 16px;">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 10px 18px; font-size: 13px;">
                <i class="bi bi-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 13px;">
                <i class="bi bi-save-fill"></i> Simpan Perubahan Akun
            </button>
        </div>
    </form>
</div>
@endsection
