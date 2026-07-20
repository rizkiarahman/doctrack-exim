@extends('layouts.app')

@section('title', 'Berbagi File')
@section('header_title', 'Berbagi File & Drive Tim')
@section('header_subtitle', 'Pusat penyimpanan dan berbagi dokumen internal PT. Detpak Indonesia')

@section('content')
<!-- Top Storage & File Stats Grid -->
<div class="grid-stats" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(99, 102, 241, 0.12); color: #818cf8;">
            <i class="bi bi-folder-symlink-fill"></i>
        </div>
        <div class="stat-details">
            <span class="stat-title">Total Berkas Dibagikan</span>
            <h3 class="stat-value">{{ $totalFilesCount }} <span style="font-size: 14px; font-weight: normal; color: var(--text-muted);">file</span></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(6, 182, 212, 0.12); color: var(--color-accent);">
            <i class="bi bi-hdd-network-fill"></i>
        </div>
        <div class="stat-details">
            <span class="stat-title">Penyimpanan Terpakai</span>
            <h3 class="stat-value" style="color: var(--color-accent);">{{ $formattedTotalStorage }}</h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: var(--color-success);">
            <i class="bi bi-cloud-arrow-up-fill"></i>
        </div>
        <div class="stat-details">
            <span class="stat-title">Unggahan Saya</span>
            <h3 class="stat-value" style="color: var(--color-success);">{{ $myFilesCount }} <span style="font-size: 14px; font-weight: normal; color: var(--text-muted);">file</span></h3>
        </div>
    </div>
</div>

<!-- Drag & Drop Upload Zone Card -->
<div class="card" style="margin-bottom: 24px; padding: 24px;">
    <div class="section-header" style="margin-bottom: 16px;">
        <h3 class="section-title" style="font-size: 16px;">
            <i class="bi bi-cloud-upload-fill text-accent" style="color: var(--color-accent);"></i> Unggah Berkas Baru
        </h3>
        <span style="font-size: 11px; color: var(--text-muted);">Batas ukuran maksimum berkas: 50 MB</span>
    </div>

    <form action="{{ route('shared-files.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
        @csrf
        <div id="drive-dropzone" style="border: 2px dashed var(--glass-border); border-radius: var(--radius-md); padding: 35px 20px; text-align: center; cursor: pointer; background: rgba(255,255,255,0.01); transition: var(--transition-smooth);">
            <i class="bi bi-cloud-arrow-up-fill" style="font-size: 44px; color: var(--color-primary); display: block; margin-bottom: 10px;"></i>
            <p style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Pilih atau Tarik Berkas ke Sini untuk Mengunggah</p>
            <span style="font-size: 12px; color: var(--text-muted);">Mendukung PDF, Excel, Word, Gambar, ZIP, dan dokumen lainnya</span>
            <input type="file" name="file" id="drive-file-input" style="display: none;" onchange="document.getElementById('upload-form').submit()">
        </div>
    </form>
</div>

<!-- Toolbar Search & Category Filters -->
<div class="toolbar" style="margin-bottom: 20px;">
    <form action="{{ route('shared-files.index') }}" method="GET" style="display: flex; width: 100%; gap: 15px; flex-wrap: wrap; align-items: center;">
        <div class="toolbar-search" style="flex: 1; min-width: 260px;">
            <i class="bi bi-search"></i>
            <input type="text" name="search" class="form-control" placeholder="Cari nama berkas..." value="{{ request('search') }}">
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('shared-files.index') }}" class="btn {{ request('category') == '' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 8px 14px; font-size: 12px;">Semua Berkas</a>
            <a href="{{ route('shared-files.index', ['category' => 'pdf', 'search' => request('search')]) }}" class="btn {{ request('category') == 'pdf' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 8px 14px; font-size: 12px;"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
            <a href="{{ route('shared-files.index', ['category' => 'document', 'search' => request('search')]) }}" class="btn {{ request('category') == 'document' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 8px 14px; font-size: 12px;"><i class="bi bi-file-earmark-word"></i> Dokumen</a>
            <a href="{{ route('shared-files.index', ['category' => 'image', 'search' => request('search')]) }}" class="btn {{ request('category') == 'image' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 8px 14px; font-size: 12px;"><i class="bi bi-file-earmark-image"></i> Gambar</a>
            <a href="{{ route('shared-files.index', ['category' => 'archive', 'search' => request('search')]) }}" class="btn {{ request('category') == 'archive' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 8px 14px; font-size: 12px;"><i class="bi bi-file-earmark-zip"></i> Arsip</a>
        </div>
    </form>
</div>

<!-- Files Grid Container (Google Drive Style Cards) -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; margin-bottom: 24px;">
    @forelse($files as $file)
        @php
            $cat = $file->file_category;
        @endphp
        <div class="card file-card" style="padding: 18px; position: relative; display: flex; flex-direction: column; justify-content: space-between; transition: var(--transition-smooth); border: 1px solid var(--glass-border);">
            
            <!-- Card Header: Icon & Category Label -->
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: {{ $cat['bg'] }}; color: {{ $cat['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="bi {{ $cat['icon'] }}"></i>
                    </div>
                    <span class="badge" style="font-size: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); color: var(--text-muted);">
                        {{ $cat['label'] }}
                    </span>
                </div>

                <!-- File Original Name -->
                <h4 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $file->original_name }}">
                    {{ $file->original_name }}
                </h4>

                <!-- Metadata Info -->
                <div style="font-size: 11px; color: var(--text-muted); display: flex; flex-direction: column; gap: 4px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Ukuran:</span>
                        <strong style="color: var(--text-secondary);">{{ $file->formatted_size }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Diunggah oleh:</span>
                        <strong style="color: var(--text-secondary);">{{ $file->user->name ?? 'User' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Waktu:</span>
                        <span>{{ $file->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Card Actions Footer -->
            <div style="display: flex; gap: 8px; pt-3; border-top: 1px solid var(--glass-border); padding-top: 12px;">
                <a href="{{ route('shared-files.download', $file->id) }}" class="btn btn-primary btn-sm" style="flex: 1; justify-content: center; font-size: 12px; padding: 8px;">
                    <i class="bi bi-download"></i> Unduh
                </a>

                @if(Auth::id() === $file->user_id || Auth::user()->role === 'admin')
                    <form action="{{ route('shared-files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas ini dari Drive?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 8px 12px;" title="Hapus Berkas">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </form>
                @endif
            </div>

        </div>
    @empty
        <div class="card" style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; color: var(--text-muted);">
            <i class="bi bi-folder2-open" style="font-size: 56px; opacity: 0.3; display: block; margin-bottom: 14px;"></i>
            <h4 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px;">Belum Ada Berkas yang Dibagikan</h4>
            <p style="font-size: 12px; max-width: 450px; margin: 0 auto 16px auto; color: var(--text-muted);">
                Semua anggota tim dapat mengunggah dan membagikan dokumen kerja di sini. Tarik atau pilih berkas di atas untuk mulai berbagi!
            </p>
        </div>
    @endforelse
</div>

<!-- Pagination Wrapper -->
@if($files->hasPages())
    <div class="card" style="padding: 15px;">
        <div class="pagination-wrapper" style="margin: 0;">
            <div class="pagination-container">
                <div>
                    Menampilkan {{ $files->firstItem() }} - {{ $files->lastItem() }} dari {{ $files->total() }} berkas
                </div>
                <div class="pagination-links">
                    @if ($files->onFirstPage())
                        <span class="disabled">&lsaquo;</span>
                    @else
                        <a href="{{ $files->previousPageUrl() }}" rel="prev">&lsaquo;</a>
                    @endif

                    @foreach ($files->getUrlRange(1, $files->lastPage()) as $page => $url)
                        @if ($page == $files->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($files->hasMorePages())
                        <a href="{{ $files->nextPageUrl() }}" rel="next">&rsaquo;</a>
                    @else
                        <span class="disabled">&rsaquo;</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropzone = document.getElementById('drive-dropzone');
        const fileInput = document.getElementById('drive-file-input');

        if (dropzone && fileInput) {
            dropzone.addEventListener('click', () => fileInput.click());

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = 'var(--color-primary)';
                dropzone.style.background = 'var(--glass-highlight)';
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.style.borderColor = 'var(--glass-border)';
                dropzone.style.background = 'rgba(255,255,255,0.01)';
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = 'var(--glass-border)';
                dropzone.style.background = 'rgba(255,255,255,0.01)';
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    document.getElementById('upload-form').submit();
                }
            });
        }
    });
</script>
@endsection
