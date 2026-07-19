@extends('layouts.app')

@section('title', 'Semua Dokumen')
@section('header_title', 'Daftar Dokumen EXIM')
@section('header_subtitle', 'Manajemen data serah terima tanda tangan dokumen')

@section('content')
<!-- Toolbar Search & Filter -->
<div class="toolbar">
    <form action="{{ route('documents.index') }}" method="GET" style="display: flex; width: 100%; gap: 15px; flex-wrap: wrap; align-items: center;">
        <!-- Search -->
        <div class="toolbar-search">
            <i class="bi bi-search"></i>
            <input type="text" name="search" class="form-control" placeholder="Cari No. AJU atau PIC..." value="{{ request('search') }}">
        </div>

        <div class="toolbar-filters">
            <div class="custom-select-wrapper" id="status-filter-select">
                <input type="hidden" name="status" id="status-filter-input" value="{{ request('status') }}">
                <div class="custom-select-trigger">
                    <span>
                        @if(request('status') == 'Menunggu Tanda Tangan')
                            <span class="dot yellow"></span> Menunggu TTD
                        @elseif(request('status') == 'Sudah Kembali')
                            <span class="dot green"></span> Sudah Kembali
                        @elseif(request('status') == 'Perlu Follow Up')
                            <span class="dot red"></span> Perlu Follow Up
                        @else
                            Semua Status
                        @endif
                    </span>
                    <i class="bi bi-chevron-down chevron"></i>
                </div>
                <div class="custom-options">
                    <div class="custom-option {{ request('status') == '' ? 'selected' : '' }}" data-value="">
                        Semua Status
                    </div>
                    <div class="custom-option {{ request('status') == 'Menunggu Tanda Tangan' ? 'selected' : '' }}" data-value="Menunggu Tanda Tangan">
                        <span class="dot yellow"></span> Menunggu TTD
                    </div>
                    <div class="custom-option {{ request('status') == 'Sudah Kembali' ? 'selected' : '' }}" data-value="Sudah Kembali">
                        <span class="dot green"></span> Sudah Kembali
                    </div>
                    <div class="custom-option {{ request('status') == 'Perlu Follow Up' ? 'selected' : '' }}" data-value="Perlu Follow Up">
                        <span class="dot red"></span> Perlu Follow Up
                    </div>
                </div>
            </div>
            
            @if(request('search') || request('status'))
                <a href="{{ route('documents.index') }}" class="btn btn-secondary" style="padding: 10px 14px;"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
    
    @if(Auth::check() && Auth::user()->role === 'admin')
    <div>
        <a href="{{ route('documents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Dokumen</span>
        </a>
    </div>
    @endif
</div>

<!-- Documents List Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table-glass">
            <thead>
                <tr>
                    <th>No. AJU</th>
                    <th>PIC</th>
                    <th>Tgl. Diserahkan</th>
                    <th>Tgl. Kembali</th>
                    <th>Status</th>
                    <th>Pending Hari</th>
                    <th>Catatan</th>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                    <th style="text-align: right; width: 150px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr style="@if($doc->status == 'Perlu Follow Up') background-color: rgba(244, 63, 94, 0.02); @endif">
                        <td><strong style="font-size: 15px; color: var(--text-primary);">{{ $doc->no_aju }}</strong></td>
                        <td>{{ $doc->pic }}</td>
                        <td>{{ $doc->tgl_diserahkan->format('d-m-Y') }}</td>
                        <td>
                            @if($doc->tgl_kembali)
                                <span class="text-success" style="font-weight: 500;"><i class="bi bi-calendar-check-fill"></i> {{ $doc->tgl_kembali->format('d-m-Y') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($doc->status == 'Sudah Kembali')
                                <span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Sudah Kembali</span>
                            @elseif($doc->status == 'Perlu Follow Up')
                                <span class="badge badge-danger"><i class="bi bi-exclamation-triangle-fill"></i> Perlu Follow Up</span>
                            @else
                                <span class="badge badge-warning"><i class="bi bi-hourglass-amber"></i> Menunggu TTD</span>
                            @endif
                        </td>
                        <td>
                            @if($doc->status == 'Sudah Kembali')
                                <span class="text-muted">Selesai ({{ $doc->days_pending }} hari)</span>
                            @else
                                <span style="font-weight: 600; color: {{ $doc->status == 'Perlu Follow Up' ? 'var(--color-danger)' : 'var(--color-warning)' }}">
                                    {{ $doc->days_pending }} Hari
                                </span>
                            @endif
                        </td>
                        <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $doc->catatan }}">
                            {{ $doc->catatan ?? '-' }}
                        </td>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <a href="{{ route('documents.edit', $doc->id) }}" class="btn btn-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::check() && Auth::user()->role === 'admin' ? 8 : 7 }}" style="text-align: center; padding: 50px; color: var(--text-muted);">
                            <i class="bi bi-folder2-open" style="font-size: 40px; color: var(--text-muted); display: block; margin-bottom: 12px;"></i>
                            Tidak ada dokumen yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Wrapper -->
    @if($documents->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-container">
                <div>
                    Menampilkan {{ $documents->firstItem() }} - {{ $documents->lastItem() }} dari {{ $documents->total() }} dokumen
                </div>
                <div class="pagination-links">
                    {{-- Previous Page Link --}}
                    @if ($documents->onFirstPage())
                        <span class="disabled">&lsaquo;</span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" rel="prev">&lsaquo;</a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($documents->getUrlRange(1, $documents->lastPage()) as $page => $url)
                        @if ($page == $documents->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($documents->hasMorePages())
                        <a href="{{ $documents->nextPageUrl() }}" rel="next">&rsaquo;</a>
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
        const wrapper = document.getElementById('status-filter-select');
        if (wrapper) {
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const options = wrapper.querySelectorAll('.custom-option');
            const input = document.getElementById('status-filter-input');
            const form = wrapper.closest('form');

            // Toggle Open/Close
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                wrapper.classList.toggle('open');
            });

            // Close when clicking outside
            document.addEventListener('click', function () {
                wrapper.classList.remove('open');
            });

            // Option selection
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
