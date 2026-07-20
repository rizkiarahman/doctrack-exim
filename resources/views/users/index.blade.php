@extends('layouts.app')

@section('title', 'Kelola Akun')
@section('header_title', 'Kelola Akun Pengguna')
@section('header_subtitle', 'Manajemen data pengguna dan pembagian hak akses sistem')

@section('content')
<!-- Toolbar Search & Filter -->
<div class="toolbar">
    <form action="{{ route('users.index') }}" method="GET" style="display: flex; width: 100%; gap: 15px; flex-wrap: wrap; align-items: center;">
        <!-- Search -->
        <div class="toolbar-search">
            <i class="bi bi-search"></i>
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
        </div>

        <!-- Filter Role -->
        <div class="toolbar-filters">
            <div class="custom-select-wrapper" id="role-filter-select">
                <input type="hidden" name="role" id="role-filter-input" value="{{ request('role') }}">
                <div class="custom-select-trigger">
                    <span>
                        @if(request('role') == 'admin')
                            <span class="dot blue"></span> Administrator
                        @elseif(request('role') == 'user')
                            <span class="dot green"></span> Staff / User
                        @else
                            Semua Peran (Role)
                        @endif
                    </span>
                    <i class="bi bi-chevron-down chevron"></i>
                </div>
                <div class="custom-options">
                    <div class="custom-option {{ request('role') == '' ? 'selected' : '' }}" data-value="">
                        Semua Peran (Role)
                    </div>
                    <div class="custom-option {{ request('role') == 'admin' ? 'selected' : '' }}" data-value="admin">
                        <span class="dot blue"></span> Administrator
                    </div>
                    <div class="custom-option {{ request('role') == 'user' ? 'selected' : '' }}" data-value="user">
                        <span class="dot green"></span> Staff / User
                    </div>
                </div>
            </div>
            
            @if(request('search') || request('role'))
                <a href="{{ route('users.index') }}" class="btn btn-secondary" style="padding: 10px 14px;"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
    
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i>
            <span>Tambah User Baru</span>
        </a>
    </div>
</div>

<!-- Users List Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table-glass">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Peran (Role)</th>
                    <th>Tanggal Terdaftar</th>
                    <th style="text-align: right; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="avatar" style="width: 36px; height: 36px; font-size: 16px;">
                                    <i class="bi bi-person-circle text-accent" style="color: var(--color-accent);"></i>
                                </div>
                                <div>
                                    <strong style="font-size: 14px; color: var(--text-primary);">{{ $user->name }}</strong>
                                    @if(Auth::id() == $user->id)
                                        <span class="badge badge-warning" style="font-size: 10px; padding: 2px 8px; margin-left: 6px;">Akun Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge badge-warning" style="background: rgba(99, 102, 241, 0.15); border-color: rgba(99, 102, 241, 0.3); color: #818cf8;"><i class="bi bi-shield-lock-fill"></i> Administrator</span>
                            @else
                                <span class="badge badge-success"><i class="bi bi-person-check-fill"></i> Staff / User</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d-m-Y H:i') }}</td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary btn-sm" title="Edit Akun">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if(Auth::id() != $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Akun">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled style="opacity: 0.4; cursor: not-allowed;" title="Tidak dapat menghapus akun sendiri">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; color: var(--text-muted);">
                            <i class="bi bi-people" style="font-size: 40px; color: var(--text-muted); display: block; margin-bottom: 12px;"></i>
                            Tidak ada data pengguna yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Wrapper -->
    @if($users->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-container">
                <div>
                    Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} user
                </div>
                <div class="pagination-links">
                    @if ($users->onFirstPage())
                        <span class="disabled">&lsaquo;</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" rel="prev">&lsaquo;</a>
                    @endif

                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if ($page == $users->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" rel="next">&rsaquo;</a>
                    @else
                        <span class="disabled">&rsaquo;</span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('role-filter-select');
        if (wrapper) {
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const options = wrapper.querySelectorAll('.custom-option');
            const input = document.getElementById('role-filter-input');
            const form = wrapper.closest('form');

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                wrapper.classList.toggle('open');
            });

            document.addEventListener('click', function () {
                wrapper.classList.remove('open');
            });

            options.forEach(opt => {
                opt.addEventListener('click', function () {
                    const value = this.getAttribute('data-value');
                    input.value = value;
                    wrapper.classList.remove('open');
                    form.submit();
                });
            });
        }
    });
</script>
@endsection
