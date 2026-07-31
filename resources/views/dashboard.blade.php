@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')
@section('header_subtitle', 'Sistem Monitoring Tanda Tangan Dokumen EXIM')

@section('content')
<!-- Stat Cards Row -->
<div class="grid-stats">
    <!-- Stat 1: Total Aktif -->
    <div class="card stat-card stat-primary">
        <div class="stat-card-header">
            <span class="stat-title">Total Dokumen Aktif</span>
            <div class="stat-icon">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
        </div>
        <div>
            <div class="stat-value">{{ $total_aktif }}</div>
            <div class="stat-desc">Belum dikembalikan supervisor</div>
        </div>
    </div>

    <!-- Stat 2: Menunggu Tanda Tangan -->
    <div class="card stat-card stat-warning">
        <div class="stat-card-header">
            <span class="stat-title">Menunggu TTD</span>
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
        <div>
            <div class="stat-value">{{ $menunggu_tanda_tangan }}</div>
            <div class="stat-desc">Dalam batas waktu normal (&lt; 7 hari)</div>
        </div>
    </div>

    <!-- Stat 3: Sudah Kembali -->
    <div class="card stat-card stat-success">
        <div class="stat-card-header">
            <span class="stat-title">Sudah Kembali</span>
            <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        <div>
            <div class="stat-value">{{ $sudah_kembali }}</div>
            <div class="stat-desc">Dokumen selesai ditandatangani</div>
        </div>
    </div>

    <!-- Stat 4: Lewat Deadline -->
    <div class="card stat-card stat-danger">
        <div class="stat-card-header">
            <span class="stat-title">Lewat Deadline</span>
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>
        <div>
            <div class="stat-value">{{ $lewat_deadline }}</div>
            <div class="stat-desc">Pending lebih dari 7 hari</div>
        </div>
    </div>

    <!-- Stat 5: Rata-rata Waktu TTD -->
    <div class="card stat-card stat-accent">
        <div class="stat-card-header">
            <span class="stat-title">Rata-rata Waktu TTD</span>
            <div class="stat-icon">
                <i class="bi bi-speedometer2"></i>
            </div>
        </div>
        <div>
            <div class="stat-value">{{ $rata_rata_hari }} <span style="font-size: 14px; font-weight: 500;">Hari</span></div>
            <div class="stat-desc">Rata-rata pengembalian dokumen</div>
        </div>
    </div>
</div>

<!-- Extra Stat: Dokumen Terlama Belum Kembali -->
@if($dokumen_terlama)
<div class="card" style="margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; gap: 20px; border-left: 4px solid var(--color-primary);">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div class="stat-icon" style="background-color: rgba(99, 102, 241, 0.15); color: var(--color-primary); width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 24px;">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
            <h4 style="font-family: var(--font-heading); font-size: 15px; font-weight: 700;">Dokumen Pending Terlama</h4>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">
                No. AJU: <strong class="text-accent" style="color: var(--color-accent);">{{ $dokumen_terlama->no_aju }}</strong> 
                | PIC: <strong>{{ $dokumen_terlama->pic }}</strong> 
                | Diserahkan: <strong>{{ $dokumen_terlama->tgl_diserahkan->format('d M Y') }}</strong>
            </p>
        </div>
    </div>
    <div style="text-align: right;">
        <span class="badge {{ $dokumen_terlama->status == 'Perlu Follow Up' ? 'badge-danger' : 'badge-warning' }}" style="font-size: 13px; padding: 6px 14px;">
            {{ $dokumen_terlama->days_pending }} Hari Pending
        </span>
    </div>
</div>
@endif

<!-- To-Do List & Daily Task Statistics Row -->
<div class="card" style="margin-bottom: 30px; padding: 24px; position: relative; overflow: hidden; border-left: 4px solid var(--color-warning);">
    <div style="position: absolute; width: 180px; height: 180px; background: radial-gradient(circle, rgba(245,158,11,0.06) 0%, transparent 70%); top: -40px; right: -40px; pointer-events: none;"></div>
    
    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--glass-border); padding-bottom: 14px; margin-bottom: 16px;">
        <div>
            <h3 style="font-family: var(--font-heading); font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-calendar-check-fill text-warning" style="color: var(--color-warning) !important;"></i>
                Jadwal & Daftar Tugas Hari Ini
            </h3>
            <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0 0 0;">Status progres dan rincian tugas harian Anda</p>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="text-align: right;">
                <span id="dash-live-time" style="font-size: 14px; font-weight: 700; color: var(--text-primary); font-family: monospace;">00:00:00</span>
            </div>
            <a href="{{ route('todo.index') }}" class="btn btn-primary btn-sm" style="padding: 6px 14px; font-size: 11px;">
                <i class="bi bi-gear-fill"></i> Kelola Tugas
            </a>
        </div>
    </div>

    <!-- Progress & Tasks Timeline Row -->
    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 24px;">
        <!-- Left: Progress Bar -->
        <div style="display: flex; flex-direction: column; justify-content: center; background: rgba(255,255,255,0.015); border: 1px solid var(--glass-border); padding: 16px; border-radius: var(--radius-md);">
            <span style="font-size: 11px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 6px;">PROGRES HARI INI</span>
            <div style="display: flex; align-items: baseline; gap: 6px; margin-bottom: 8px;">
                <span style="font-size: 28px; font-weight: 800; color: var(--text-primary);">{{ $todoProgress }}%</span>
                <span style="font-size: 11px; color: var(--color-success); font-weight: 600;">Selesai</span>
            </div>
            <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 12px;">
                <div style="width: {{ $todoProgress }}%; height: 100%; background: linear-gradient(90deg, var(--color-primary), var(--color-success));"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted);">
                <span>Total: <strong>{{ $todoTasks->count() }}</strong></span>
                <span>Selesai: <strong class="text-success">{{ $todoTasks->where('status', 'selesai')->count() }}</strong></span>
            </div>
        </div>

        <!-- Right: Horizontally Scrollable or Compact Timeline List -->
        <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 8px;">
            @forelse($todoTasks as $t)
                @php
                    $badgeColor = 'grey';
                    $borderColor = 'var(--glass-border)';
                    if ($t->status === 'sedang_dikerjakan') {
                        $badgeColor = 'var(--color-primary)';
                        $borderColor = 'rgba(99, 102, 241, 0.25)';
                    } elseif ($t->status === 'menunggu') {
                        $badgeColor = 'var(--color-warning)';
                        $borderColor = 'rgba(245, 158, 11, 0.25)';
                    } elseif ($t->status === 'selesai') {
                        $badgeColor = 'var(--color-success)';
                        $borderColor = 'rgba(16, 185, 129, 0.25)';
                    } elseif ($t->status === 'terlambat') {
                        $badgeColor = 'var(--color-danger)';
                        $borderColor = 'rgba(239, 68, 68, 0.25)';
                    }
                @endphp
                <div style="min-width: 180px; max-width: 180px; background: rgba(255,255,255,0.01); border: 1px solid {{ $borderColor }}; border-top: 3px solid {{ $badgeColor }}; padding: 12px; border-radius: var(--radius-sm); display: flex; flex-direction: column; justify-content: space-between; height: 115px;">
                    <div>
                        <span style="font-size: 9px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">{{ $t->time_start }} - {{ $t->time_end }}</span>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;" title="{{ $t->title }}">
                            {{ $t->title }}
                        </p>
                    </div>
                    <span style="font-size: 9px; font-weight: 600; color: {{ $badgeColor }}; text-transform: uppercase; margin-top: 5px; display: flex; align-items: center; gap: 4px;">
                        <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $badgeColor }};"></span>
                        {{ str_replace('_', ' ', $t->status) }}
                    </span>
                </div>
            @empty
                <div style="display: flex; align-items: center; justify-content: center; width: 100%; color: var(--text-muted); font-size: 12px;">
                    Belum ada tugas hari ini.
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeEl = document.getElementById('dash-live-time');
        if (timeEl) {
            setInterval(() => {
                const now = new Date();
                timeEl.textContent = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
            }, 1000);
        }
    });
</script>

<!-- Dashboard Columns: AI Assistant and Recent Documents -->
<div class="dashboard-columns">
    <!-- Left Column: Recent Documents -->
    <div class="card">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-list-stars text-accent" style="color: var(--color-accent);"></i>
                Aktivitas Dokumen Terbaru
            </h3>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>

        <div class="table-responsive">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>No. AJU</th>
                        <th>PIC</th>
                        <th>Tgl. Diserahkan</th>
                        <th>Status</th>
                        <th>Pending</th>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <th style="text-align: right;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_documents as $doc)
                        <tr>
                            <td><strong>{{ $doc->no_aju }}</strong></td>
                            <td>{{ $doc->pic }}</td>
                            <td>{{ $doc->tgl_diserahkan->format('d-m-Y') }}</td>
                            <td>
                                @if($doc->status == 'Sudah Kembali')
                                    <span class="badge badge-success"><i class="bi bi-check-circle"></i> Sudah Kembali</span>
                                @elseif($doc->status == 'Perlu Follow Up')
                                    <span class="badge badge-danger"><i class="bi bi-exclamation-triangle"></i> Perlu Follow Up</span>
                                @else
                                    <span class="badge badge-warning"><i class="bi bi-hourglass-amber"></i> Menunggu TTD</span>
                                @endif
                            </td>
                            <td>
                                @if($doc->status == 'Sudah Kembali')
                                    <span class="text-muted">Selesai ({{ $doc->days_pending }} hari)</span>
                                @else
                                    <span style="font-weight: 500; color: {{ $doc->status == 'Perlu Follow Up' ? 'var(--color-danger)' : 'var(--text-primary)' }}">{{ $doc->days_pending }} hari</span>
                                @endif
                            </td>
                            @if(Auth::check() && Auth::user()->role === 'admin')
                            <td style="text-align: right;">
                                <a href="{{ route('documents.edit', $doc->id) }}" class="btn btn-secondary btn-sm" title="Edit Dokumen">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Belum ada dokumen terdaftar. <a href="{{ route('documents.create') }}" class="text-accent" style="color: var(--color-accent); text-decoration: none;">Tambah Baru</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: AI Assistant Warning Widget -->
    <div class="card ai-panel">
        <div class="ai-header">
            <div class="ai-avatar">
                <i class="bi bi-cpu-fill"></i>
            </div>
            <div class="ai-header-text">
                <h3>EXIM AI Assistant</h3>
                <p>Pemantau tenggat waktu dokumen otomatis</p>
            </div>
        </div>

        <div class="ai-warnings-list">
            @forelse($ai_warnings as $warning)
                <div class="ai-warning-item">
                    <div class="ai-warning-title">
                        <h4>⚠️ TINDAKAN DIBUTUHKAN</h4>
                        <span class="ai-warning-badge">{{ $warning['days'] }} Hari Pending</span>
                    </div>
                    <p class="ai-warning-text">{{ $warning['message'] }}</p>
                    <div class="ai-warning-actions">
                        <a href="{{ $warning['wa_link'] }}" target="_blank" class="btn-ai-action whatsapp">
                            <i class="bi bi-whatsapp"></i> Hubungi PIC
                        </a>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <a href="{{ route('documents.edit', $warning['id']) }}" class="btn-ai-action edit">
                            <i class="bi bi-pencil-square"></i> Update TTD
                        </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="ai-no-warning">
                    <i class="bi bi-shield-check"></i>
                    <h4>Semua Aman!</h4>
                    <p>Tidak ada dokumen aktif yang melebihi batas waktu 7 hari. AI asisten akan memonitor kembali secara otomatis.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
