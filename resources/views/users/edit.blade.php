@extends('layouts.app')

@section('title', 'Edit User')
@section('header_title', 'Edit Akun Pengguna')
@section('header_subtitle', 'Perbarui data pengguna, kata sandi, atau hak akses peran')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto; position: relative; overflow: hidden;">
    <div style="position: absolute; width: 150px; height: 150px; background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%); top: -50px; right: -50px; pointer-events: none;"></div>
    
    <div class="section-header" style="margin-bottom: 24px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
        <h3 class="section-title">
            <i class="bi bi-pencil-square text-accent" style="color: var(--color-accent);"></i>
            Edit Akun Pengguna: {{ $user->name }}
        </h3>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Nama Pengguna -->
        <div class="form-group">
            <label for="name">Nama Lengkap <span class="text-danger" style="color: var(--color-danger);">*</span></label>
            <div style="position: relative;">
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Budi Santoso" value="{{ old('name', $user->name) }}" required style="padding-left: 45px;">
                <i class="bi bi-person" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
            </div>
            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Alamat Email <span class="text-danger" style="color: var(--color-danger);">*</span></label>
            <div style="position: relative;">
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@detpak.co.id" value="{{ old('email', $user->email) }}" required style="padding-left: 45px;">
                <i class="bi bi-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
            </div>
            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password (Opsional) -->
        <div class="form-group">
            <label for="password">Kata Sandi Baru <span style="font-weight: normal; font-size: 11px; color: var(--text-muted);">(Biarkan kosong jika tidak ingin mengubah kata sandi)</span></label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Isi untuk mengganti kata sandi..." style="padding-left: 45px;">
                <i class="bi bi-key" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
            </div>
            @error('password')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Select -->
        <div class="form-group">
            <label for="role">Peran / Hak Akses (Role) <span class="text-danger" style="color: var(--color-danger);">*</span></label>
            <div class="role-selector" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <label style="display: flex; align-items: center; gap: 12px; padding: 16px; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border); border-radius: var(--radius-md); cursor: pointer; transition: var(--transition-smooth);" class="role-option">
                    <input type="radio" name="role" value="user" {{ old('role', $user->role) == 'user' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
                    <div>
                        <strong style="display: block; font-size: 14px; color: var(--text-primary);"><i class="bi bi-person-check-fill text-success" style="color: var(--color-success);"></i> Staff / User</strong>
                        <span style="font-size: 11px; color: var(--text-muted);">Akses membaca dashboard & dokumen (Read-only)</span>
                    </div>
                </label>
                <label style="display: flex; align-items: center; gap: 12px; padding: 16px; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border); border-radius: var(--radius-md); cursor: pointer; transition: var(--transition-smooth);" class="role-option">
                    <input type="radio" name="role" value="admin" {{ old('role', $user->role) == 'admin' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
                    <div>
                        <strong style="display: block; font-size: 14px; color: var(--text-primary);"><i class="bi bi-shield-lock-fill" style="color: #818cf8;"></i> Administrator</strong>
                        <span style="font-size: 11px; color: var(--text-muted);">Akses penuh CRUD dokumen & kelola user</span>
                    </div>
                </label>
            </div>
            @error('role')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save-fill"></i> Perbarui User
            </button>
        </div>
    </form>
</div>
@endsection
