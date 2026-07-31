@extends('layouts.app')

@section('title', 'Edit Dokumen')
@section('header_title', 'Perbarui Dokumen')
@section('header_subtitle', 'Ubah data serah terima berkas EXIM atau update tanggal pengembalian')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto; position: relative; overflow: hidden;">
    <!-- Decorative Glow Blobs inside the card -->
    <div style="position: absolute; width: 150px; height: 150px; background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%); top: -50px; right: -50px; pointer-events: none;"></div>

    <div class="section-header" style="margin-bottom: 24px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
        <h3 class="section-title">
            <i class="bi bi-pencil-square text-accent" style="color: var(--color-accent); text-shadow: 0 0 10px rgba(6, 182, 212, 0.3);"></i>
            Edit Dokumen: <span style="font-family: monospace;">{{ $document->no_aju }}</span>
        </h3>
        <a href="{{ route('documents.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Live Status Preview Badge -->
    <div style="margin-bottom: 30px; padding: 18px 24px; background: rgba(255, 255, 255, 0.01); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: space-between; gap: 20px; backdrop-filter: blur(5px);">
        <div>
            <h4 style="font-family: var(--font-heading); font-size: 14px; font-weight: 700; color: var(--text-primary);">Status Dokumen (Real-time)</h4>
            <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Status dikalkulasi secara otomatis oleh sistem.</p>
        </div>
        <div id="live-status-badge">
            <!-- Populated by JS -->
        </div>
    </div>

    <form action="{{ route('documents.update', $document->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <!-- No. AJU -->
            <div class="form-group">
                <label for="no_aju">No. AJU (Nomor Pengajuan) <span class="text-danger" style="color: var(--color-danger);">*</span></label>
                <div style="position: relative;">
                    <input type="text" name="no_aju" id="no_aju" class="form-control @error('no_aju') is-invalid @enderror" placeholder="Contoh: AJU-2026-0719" value="{{ old('no_aju', $document->no_aju) }}" required style="padding-left: 45px;">
                    <i class="bi bi-hash" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
                </div>
                @error('no_aju')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- PIC -->
            <div class="form-group">
                <label for="pic">Nama PIC (Penanggung Jawab) <span class="text-danger" style="color: var(--color-danger);">*</span></label>
                <div style="position: relative;">
                    <input type="text" name="pic" id="pic" class="form-control @error('pic') is-invalid @enderror" placeholder="Contoh: Ahmad Fauzi" value="{{ old('pic', $document->pic) }}" required style="padding-left: 45px;">
                    <i class="bi bi-person-fill" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
                </div>
                @error('pic')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-grid">
            <!-- Tanggal Diserahkan -->
            <div class="form-group">
                <label for="tgl_diserahkan">Tanggal Diserahkan <span class="text-danger" style="color: var(--color-danger);">*</span></label>
                <div style="position: relative;">
                    <input type="date" name="tgl_diserahkan" id="tgl_diserahkan" class="form-control @error('tgl_diserahkan') is-invalid @enderror" value="{{ old('tgl_diserahkan', $document->tgl_diserahkan->format('Y-m-d')) }}" required style="padding-left: 45px;">
                    <i class="bi bi-calendar-plus" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
                </div>
                @error('tgl_diserahkan')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Kembali -->
            <div class="form-group">
                <label for="tgl_kembali">Tanggal Kembali (Isi untuk menyelesaikan)</label>
                <div style="position: relative;">
                    <input type="date" name="tgl_kembali" id="tgl_kembali" class="form-control @error('tgl_kembali') is-invalid @enderror" value="{{ old('tgl_kembali', $document->tgl_kembali ? $document->tgl_kembali->format('Y-m-d') : '') }}" style="padding-left: 45px;">
                    <i class="bi bi-calendar-check" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
                </div>
                @error('tgl_kembali')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Catatan -->
        <div class="form-group">
            <label for="catatan">Catatan / Keterangan Tambahan (Opsional)</label>
            <div style="position: relative;">
                <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror" placeholder="Tuliskan catatan tambahan mengenai berkas ini..." style="padding-left: 45px;">{{ old('catatan', $document->catatan) }}</textarea>
                <i class="bi bi-sticky" style="position: absolute; left: 16px; top: 20px; color: var(--text-muted); font-size: 16px;"></i>
            </div>
            @error('catatan')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save-fill"></i> Perbarui Dokumen
            </button>
        </div>
    </form>
</div>

<!-- Real-time Status Calculator Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tglDiserahkanInput = document.getElementById('tgl_diserahkan');
        const tglKembaliInput = document.getElementById('tgl_kembali');
        const badgeContainer = document.getElementById('live-status-badge');

        function updatePreviewStatus() {
            const diserahkanVal = tglDiserahkanInput.value;
            const kembaliVal = tglKembaliInput.value;

            if (!diserahkanVal) return;

            const diserahkanDate = new Date(diserahkanVal);
            diserahkanDate.setHours(0, 0, 0, 0);

            let statusHtml = '';

            if (kembaliVal) {
                // If returned
                const kembaliDate = new Date(kembaliVal);
                kembaliDate.setHours(0, 0, 0, 0);

                // Calculate difference
                const diffTime = Math.abs(kembaliDate - diserahkanDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                statusHtml = `<span class="badge badge-success" style="font-size: 13px; padding: 8px 16px; box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);"><i class="bi bi-check-circle-fill"></i> Sudah Kembali (Selesai ${diffDays} Hari)</span>`;
            } else {
                // Pending
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const diffTime = today - diserahkanDate;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays >= 7) {
                    statusHtml = `<span class="badge badge-danger" style="font-size: 13px; padding: 8px 16px; box-shadow: 0 0 15px rgba(244, 63, 94, 0.2);"><i class="bi bi-exclamation-triangle-fill"></i> Perlu Follow Up (${diffDays} Hari Pending)</span>`;
                } else {
                    const displayDays = diffDays >= 0 ? diffDays : 0;
                    statusHtml = `<span class="badge badge-warning" style="font-size: 13px; padding: 8px 16px; box-shadow: 0 0 15px rgba(245, 158, 11, 0.2);"><i class="bi bi-hourglass-amber"></i> Menunggu TTD (${displayDays} Hari Pending)</span>`;
                }
            }

            badgeContainer.innerHTML = statusHtml;
        }

        tglDiserahkanInput.addEventListener('change', updatePreviewStatus);
        tglKembaliInput.addEventListener('change', updatePreviewStatus);

        // Run initial calculation
        updatePreviewStatus();
    });
</script>
@endsection