@extends('layouts.app')

@section('title', 'Sunting PDF')
@section('header_title', 'Sunting & Kelola Berkas PDF')
@section('header_subtitle', 'Sunting lampiran PDF, gabungkan halaman, dan ekspor hasil suntingan dalam format PDF')

@section('content')
<!-- Include CDN Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    }
</script>

<div class="dashboard-columns" style="grid-template-columns: 340px 1fr; gap: 20px;">

    <!-- Left Controls Panel -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Upload Card -->
        <div class="card" style="padding: 20px;">
            <h3 class="section-title" style="font-size: 16px; margin-bottom: 14px;">
                <i class="bi bi-file-earmark-pdf-fill text-danger" style="color: var(--color-danger);"></i> Unggah PDF
            </h3>
            
            <div id="drop-zone" style="border: 2px dashed var(--glass-border); border-radius: var(--radius-md); padding: 30px 15px; text-align: center; cursor: pointer; background: rgba(255,255,255,0.01); transition: var(--transition-smooth);">
                <i class="bi bi-cloud-arrow-up-fill" style="font-size: 38px; color: var(--color-primary); display: block; margin-bottom: 8px;"></i>
                <p style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Pilih atau Tarik PDF ke Sini</p>
                <span style="font-size: 11px; color: var(--text-muted);">Mendukung berkas tunggal atau banyak (.pdf, Maks. 20 MB)</span>
                <input type="file" id="pdf-upload" accept=".pdf, .PDF, application/pdf, application/x-pdf" multiple style="display: none;">
            </div>

            <div id="file-info" style="margin-top: 15px; display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 10px 12px; border-radius: var(--radius-sm);">
                    <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                        <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 20px;"></i>
                        <div style="overflow: hidden;">
                            <p id="file-name" style="font-size: 12px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"></p>
                            <span id="file-pages-count" style="font-size: 10px; color: var(--text-muted);"></span>
                        </div>
                    </div>
                    <button type="button" id="btn-remove-file" class="btn btn-secondary btn-sm" style="padding: 4px 8px;" title="Hapus berkas"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>

        <!-- Customization Settings Card -->
        <div class="card" style="padding: 20px;">
            <h3 class="section-title" style="font-size: 16px; margin-bottom: 14px;">
                <i class="bi bi-sliders text-accent" style="color: var(--color-accent);"></i> Pengaturan Sunting
            </h3>

            <!-- Action Mode Selection (Custom Glassmorphic Dropdown) -->
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="font-size: 12px; margin-bottom: 6px; display: block; font-weight: 500;">Mode Penyuntingan</label>
                <div class="custom-select-wrapper" id="pdf-action-mode-select" style="width: 100%;">
                    <input type="hidden" id="action-mode" value="split">
                    <div class="custom-select-trigger" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-smooth);">
                        <span id="pdf-action-mode-selected" style="font-size: 13px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-pencil-square" style="color: var(--color-primary);"></i> Sunting Lampiran PDF
                        </span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </div>
                    <div class="custom-options" style="border-radius: var(--radius-sm); z-index: 100;">
                        <div class="custom-option selected" data-value="split" style="padding: 10px 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="bi bi-pencil-square" style="color: var(--color-primary); font-size: 16px;"></i>
                                <div>
                                    <strong style="display: block; font-size: 13px; color: var(--text-primary);">Sunting Lampiran PDF</strong>
                                    <span style="font-size: 10px; color: var(--text-muted);">Sunting lembar PDF (rotasi, pisahkan rentang, atau tambah stempel catatan)</span>
                                </div>
                            </div>
                        </div>
                        <div class="custom-option" data-value="merge" style="padding: 10px 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="bi bi-file-earmark-plus-fill" style="color: var(--color-accent); font-size: 16px;"></i>
                                <div>
                                    <strong style="display: block; font-size: 13px; color: var(--text-primary);">Menggabungkan PDF (Merge)</strong>
                                    <span style="font-size: 10px; color: var(--text-muted);">Gabungkan seluruh halaman terpilih menjadi 1 dokumen tunggal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Level Editing Tools (Rotasi & Stempel Catatan Teks) -->
            <div id="pdf-edit-tools-panel" style="margin-bottom: 16px; padding: 12px; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: var(--radius-sm);">
                <label style="font-size: 12px; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary);">
                    <span><i class="bi bi-tools text-primary" style="color: var(--color-primary);"></i> Alat Sunting Lembar PDF</span>
                    <span style="font-size: 10px; font-weight: normal; color: var(--text-muted);">Berfungsi Interaktif</span>
                </label>

                <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                    <button type="button" id="btn-rotate-selected" class="btn btn-secondary btn-sm" style="font-size: 11px; flex: 1;" title="Rotasi 90 Derajat Searah Jarum Jam">
                        <i class="bi bi-arrow-clockwise"></i> Rotasi 90°
                    </button>
                </div>

                <!-- Stempel Catatan Teks -->
                <div style="border-top: 1px dashed var(--glass-border); padding-top: 10px; margin-top: 6px;">
                    <label style="font-size: 11px; font-weight: 500; display: flex; align-items: center; gap: 6px; margin-bottom: 6px; cursor: pointer; color: var(--text-primary);">
                        <input type="checkbox" id="enable-watermark-checkbox" style="accent-color: var(--color-primary); cursor: pointer;">
                        <span>Tambah Stempel / Catatan Teks</span>
                    </label>

                    <div id="watermark-input-group" style="display: none; flex-direction: column; gap: 8px; margin-top: 8px;">
                        <input type="text" id="watermark-text-input" class="form-control" placeholder="Contoh: DISETUJUI EXIM - PT DETPAK" style="font-size: 12px;">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <select id="watermark-color-select" class="form-control" style="font-size: 11px; padding: 6px 10px;">
                                <option value="red">Teks Merah</option>
                                <option value="blue">Teks Biru</option>
                                <option value="black">Teks Hitam</option>
                                <option value="green">Teks Hijau</option>
                            </select>
                            <select id="watermark-pos-select" class="form-control" style="font-size: 11px; padding: 6px 10px;">
                                <option value="top-right" selected>Atas Kanan (Mark Line)</option>
                                <option value="bottom-right">Bawah Kanan</option>
                                <option value="center">Tengah</option>
                                <option value="bottom-left">Bawah Kiri</option>
                                <option value="custom">Bebas Geser (Mouse Drag)</option>
                            </select>
                        </div>

                        <!-- Font Size Selector -->
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); padding: 6px 10px; border-radius: var(--radius-sm);">
                            <span style="font-size: 11px; color: var(--text-muted);"><i class="bi bi-type"></i> Ukuran Teks:</span>
                            <select id="watermark-size-select" class="form-control" style="font-size: 11px; padding: 4px 8px; width: 100px;">
                                <option value="10">10 pt (Kecil)</option>
                                <option value="12">12 pt (Sedang)</option>
                                <option value="14" selected>14 pt (Normal)</option>
                                <option value="18">18 pt (Besar)</option>
                                <option value="24">24 pt (Ekstra)</option>
                                <option value="30">30 pt (Jumbo)</option>
                            </select>
                        </div>

                        <button type="button" id="btn-open-drag-modal" class="btn btn-secondary btn-sm" style="width: 100%; font-size: 11px; justify-content: center; gap: 6px; margin-top: 2px;">
                            <i class="bi bi-arrows-move" style="color: var(--color-primary);"></i> Atur Posisi & Ukuran Teks Interaktif (Mouse Drag)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page Selection Input -->
            <div class="form-group" id="split-pages-group" style="margin-bottom: 16px;">
                <label for="page-range" style="font-size: 12px; font-weight: 500;">Halaman yang Diinginkan</label>
                <input type="text" id="page-range" class="form-control" placeholder="Contoh: 1, 3, 5-8" style="font-size: 13px;">
                <span id="page-range-help" style="font-size: 10px; color: var(--text-muted); display: block; margin-top: 4px;">Mode Sunting Lampiran PDF: Pilih, rotasi, pisahkan rentang halaman, atau beri stempel catatan teks pada dokumen PDF.</span>
            </div>

            <!-- PDF Compression Toggle -->
            <div class="form-group" style="margin-bottom: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                    <label style="font-size: 12px; font-weight: 500; margin: 0;">Kompresi Ukuran PDF</label>
                    <span id="compress-badge" class="badge badge-warning" style="font-size: 10px; display: none; padding: 2px 8px;">Target &le; 5 MB</span>
                </div>
                
                <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-smooth);" id="compress-toggle-label">
                    <input type="checkbox" id="enable-compression" style="accent-color: var(--color-primary); width: 16px; height: 16px; cursor: pointer;">
                    <div>
                        <strong style="display: block; font-size: 12px; color: var(--text-primary);"><i class="bi bi-file-earmark-zip text-accent" style="color: var(--color-accent);"></i> Aktifkan Kompresi (Target &le; 5 MB)</strong>
                        <span style="font-size: 10px; color: var(--text-muted);">Mengompres stream & objek PDF agar ukuran berkas akhir tidak melebihi 5 MB</span>
                    </div>
                </label>
            </div>

            <!-- Smart Recommendation Banner (> 5 MB) -->
            <div id="compression-recommendation" style="display: none; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); padding: 10px 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                <div style="display: flex; gap: 10px; align-items: flex-start;">
                    <i class="bi bi-lightbulb-fill" style="font-size: 16px; color: var(--color-warning); margin-top: 2px;"></i>
                    <div style="font-size: 11px; color: var(--text-primary); line-height: 1.4;">
                        <strong style="color: var(--color-warning);">Rekomendasi Kompresi PDF</strong>
                        <p id="recommendation-text" style="margin: 2px 0 0 0; color: var(--text-secondary);"></p>
                    </div>
                </div>
            </div>

            <!-- PDF File Name -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="zip-name" style="font-size: 12px; font-weight: 500;">Nama Berkas PDF Unduhan</label>
                <input type="text" id="zip-name" class="form-control" value="Dokumen_EXIM_Sunting.pdf" style="font-size: 13px;">
            </div>

            <!-- Download PDF Button -->
            <button type="button" id="btn-download-zip" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;" disabled>
                <i class="bi bi-file-earmark-pdf-fill"></i> Download Hasil Suntingan PDF
            </button>
        </div>
    </div>

    <!-- Right Print Preview Panel (Original Clean Layout) -->
    <div class="card" style="padding: 20px; min-height: 500px; display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px;">
            <h3 class="section-title" style="font-size: 16px; margin: 0;">
                <i class="bi bi-printer-fill text-accent" style="color: var(--color-accent);"></i> Print Preview / Pratinjau Halaman
            </h3>
            <div style="display: flex; gap: 8px;">
                <button type="button" id="btn-select-all" class="btn btn-secondary btn-sm" style="font-size: 11px;" disabled>Pilih Semua</button>
                <button type="button" id="btn-deselect-all" class="btn btn-secondary btn-sm" style="font-size: 11px;" disabled>Batal Pilih</button>
            </div>
        </div>

        <!-- Empty State -->
        <div id="preview-empty" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); padding: 40px; text-align: center;">
            <i class="bi bi-images" style="font-size: 50px; opacity: 0.3; margin-bottom: 12px;"></i>
            <p style="font-size: 14px; font-weight: 500;">Belum Ada PDF yang Diunggah</p>
            <span style="font-size: 12px; opacity: 0.7;">Unggah berkas PDF di sebelah kiri untuk melihat pratinjau lembar cetak dan memisahkan/menggabungkan halaman.</span>
        </div>

        <!-- Thumbnail Grid Container -->
        <div id="preview-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; flex: 1; overflow-y: auto; max-height: 650px; padding: 5px;"></div>
    </div>
</div>

<!-- Extra Spacious Fullscreen Modal Interactive Text & Size Editor (HD Scale 2.2 + Zoom Controls) -->
<div id="pdf-drag-text-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 1000; align-items: center; justify-content: center; padding: 15px;">
    <div style="background: var(--bg-dark); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 18px 24px; width: 98vw; max-width: 1600px; height: 96vh; max-height: 96vh; display: flex; flex-direction: column; gap: 12px; box-shadow: 0 25px 80px rgba(0,0,0,0.85);">
        
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-arrows-move" style="color: var(--color-primary);"></i> Editor Posisi, Ukuran & Teks Dokumen PDF (Tampilan Fullscreen HD)
            </h4>
            <button type="button" id="btn-close-drag-modal" class="btn btn-secondary btn-sm" style="padding: 4px 10px;"><i class="bi bi-x-lg"></i> Tutup</button>
        </div>

        <!-- Interactive Toolbar (Edit Text, Color, Font Size, & Document Zoom) -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 10px 14px; border-radius: var(--radius-md);">
            <!-- Direct Text Edit Input -->
            <div style="flex: 1; min-width: 240px; display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 11px; font-weight: 600; color: var(--text-muted); white-space: nowrap;"><i class="bi bi-pencil-fill" style="color: var(--color-primary);"></i> Ubah Teks:</label>
                <input type="text" id="modal-watermark-input" class="form-control" placeholder="Tentukan isi teks catatan..." style="font-size: 12px; padding: 6px 12px;">
            </div>

            <!-- Color Select -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 11px; font-weight: 600; color: var(--text-muted); white-space: nowrap;"><i class="bi bi-palette-fill" style="color: var(--color-accent);"></i> Warna:</label>
                <select id="modal-color-select" class="form-control" style="font-size: 11px; padding: 6px 10px; width: 110px;">
                    <option value="red">Merah</option>
                    <option value="blue">Biru</option>
                    <option value="black">Hitam</option>
                    <option value="green">Hijau</option>
                </select>
            </div>

            <!-- Font Size Adjuster -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 11px; font-weight: 600; color: var(--text-muted); white-space: nowrap;"><i class="bi bi-type" style="color: var(--color-warning);"></i> Ukuran Teks:</label>
                <div style="display: flex; align-items: center; gap: 4px;">
                    <button type="button" id="btn-font-smaller" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px;" title="Kecilkan Teks (-2pt)"><i class="bi bi-dash-lg"></i></button>
                    <select id="modal-fontsize-select" class="form-control" style="font-size: 11px; padding: 6px 8px; width: 85px;">
                        <option value="10">10 pt</option>
                        <option value="12">12 pt</option>
                        <option value="14" selected>14 pt</option>
                        <option value="18">18 pt</option>
                        <option value="22">22 pt</option>
                        <option value="26">26 pt</option>
                        <option value="30">30 pt</option>
                        <option value="36">36 pt</option>
                    </select>
                    <button type="button" id="btn-font-larger" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px;" title="Besarkan Teks (+2pt)"><i class="bi bi-plus-lg"></i></button>
                </div>
            </div>

            <!-- Document Zoom Controls -->
            <div style="display: flex; align-items: center; gap: 6px; border-left: 1px solid var(--glass-border); padding-left: 12px;">
                <label style="font-size: 11px; font-weight: 600; color: var(--text-muted); white-space: nowrap;"><i class="bi bi-aspect-ratio" style="color: var(--color-accent);"></i> Zoom Lembar:</label>
                <button type="button" id="btn-zoom-out-page" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 11px;" title="Kecilkan Lembar Dokumen"><i class="bi bi-zoom-out"></i></button>
                <span id="zoom-page-level" style="font-size: 11px; font-weight: 600; color: var(--color-primary); min-width: 45px; text-align: center;">100%</span>
                <button type="button" id="btn-zoom-in-page" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 11px;" title="Besarkan Lembar Dokumen"><i class="bi bi-zoom-in"></i></button>
                <button type="button" id="btn-zoom-reset-page" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 11px;" title="Reset Zoom Lembar">Reset</button>
            </div>
        </div>
        
        <!-- Spacious Extra Large Canvas Container -->
        <div id="drag-canvas-wrapper" style="flex: 1; overflow: auto; text-align: center; background: #0b0f19; border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 25px; position: relative; display: flex; align-items: flex-start; justify-content: center; min-height: 550px;">
            <div id="canvas-zoom-container" style="position: relative; display: inline-block; transition: transform 0.2s ease; transform-origin: top center;">
                <canvas id="drag-canvas" style="box-shadow: 0 12px 40px rgba(0,0,0,0.6); border-radius: 4px; background: #fff; display: block;"></canvas>
                <div id="drag-text-badge" style="display: none; position: absolute; cursor: move; user-select: none; padding: 6px 14px; border-radius: 4px; border: 2px dashed var(--color-primary); background: rgba(15, 23, 42, 0.94); font-weight: 700; z-index: 50; box-shadow: 0 8px 25px rgba(0,0,0,0.6); transition: font-size 0.15s ease;">
                    <i class="bi bi-arrows-move" style="font-size: 11px; opacity: 0.8; margin-right: 6px;"></i> <span id="badge-drag-text-content">DISETUJUI EXIM</span>
                </div>
            </div>
        </div>

        <!-- Footer & Save Actions -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--glass-border); padding-top: 10px;">
            <span style="font-size: 11px; color: var(--text-muted);" id="drag-coord-info">Posisi Teks: X: 81%, Y: 2.5% | Ukuran: 14pt</span>
            <div style="display: flex; gap: 8px;">
                <button type="button" id="btn-cancel-drag-pos" class="btn btn-secondary btn-sm" style="padding: 8px 14px;">Batal</button>
                <button type="button" id="btn-save-drag-pos" class="btn btn-primary btn-sm" style="padding: 8px 20px;"><i class="bi bi-check-lg"></i> Simpan Teks & Posisi</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('drop-zone');
        const pdfUpload = document.getElementById('pdf-upload');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const filePagesCount = document.getElementById('file-pages-count');
        const btnRemove = document.getElementById('btn-remove-file');
        const previewGrid = document.getElementById('preview-grid');
        const previewEmpty = document.getElementById('preview-empty');
        const btnDownloadZip = document.getElementById('btn-download-zip');
        const pageRangeInput = document.getElementById('page-range');
        const pageRangeHelp = document.getElementById('page-range-help');
        const btnSelectAll = document.getElementById('btn-select-all');
        const btnDeselectAll = document.getElementById('btn-deselect-all');
        const zipNameInput = document.getElementById('zip-name');
        const pdfModeWrapper = document.getElementById('pdf-action-mode-select');

        const watermarkCheckbox = document.getElementById('enable-watermark-checkbox');
        const watermarkInput = document.getElementById('watermark-text-input');
        const watermarkColorSelect = document.getElementById('watermark-color-select');
        const watermarkPosSelect = document.getElementById('watermark-pos-select');
        const watermarkSizeSelect = document.getElementById('watermark-size-select');

        // Drag Modal Elements
        const dragModal = document.getElementById('pdf-drag-text-modal');
        const btnOpenDragModal = document.getElementById('btn-open-drag-modal');
        const btnCloseDragModal = document.getElementById('btn-close-drag-modal');
        const btnCancelDragPos = document.getElementById('btn-cancel-drag-pos');
        const btnSaveDragPos = document.getElementById('btn-save-drag-pos');
        const dragCanvas = document.getElementById('drag-canvas');
        const dragCanvasWrapper = document.getElementById('drag-canvas-wrapper');
        const canvasZoomContainer = document.getElementById('canvas-zoom-container');
        const dragTextBadge = document.getElementById('drag-text-badge');
        const badgeDragTextContent = document.getElementById('badge-drag-text-content');
        const dragCoordInfo = document.getElementById('drag-coord-info');

        // Modal Controls
        const modalWatermarkInput = document.getElementById('modal-watermark-input');
        const modalColorSelect = document.getElementById('modal-color-select');
        const modalFontSizeSelect = document.getElementById('modal-fontsize-select');
        const btnFontSmaller = document.getElementById('btn-font-smaller');
        const btnFontLarger = document.getElementById('btn-font-larger');

        // Document Zoom Controls
        const btnZoomInPage = document.getElementById('btn-zoom-in-page');
        const btnZoomOutPage = document.getElementById('btn-zoom-out-page');
        const btnZoomResetPage = document.getElementById('btn-zoom-reset-page');
        const zoomPageLevel = document.getElementById('zoom-page-level');

        let loadedPdfFiles = []; // Array of { file, pdfjsDoc, pdfLibDoc, totalPages, startIndex }
        let selectedPagesMap = new Set(); // Set of page numbers selected (1-indexed)
        let rotationMap = {}; // Map of globalIndex -> angle (0, 90, 180, 270)
        let customWatermarkPos = null; // { xPercent, yPercent } when user drags text with mouse
        let currentFontSize = 14; // Default font size in pt
        let tempDragPos = { xPercent: 0.81, yPercent: 0.025 };
        let currentDocumentZoom = 1.0; // Zoom factor for document preview (1.0 = 100%)
        let totalGlobalPages = 0;

        function getPresetPositionPercent(posVal) {
            switch (posVal) {
                case 'top-right': return { xPercent: 0.81, yPercent: 0.025 };
                case 'center': return { xPercent: 0.35, yPercent: 0.45 };
                case 'bottom-left': return { xPercent: 0.08, yPercent: 0.86 };
                case 'bottom-right':
                default:
                    return { xPercent: 0.70, yPercent: 0.86 };
            }
        }

        function getWatermarkColorHex(colorVal) {
            switch (colorVal) {
                case 'blue': return '#3b82f6';
                case 'black': return '#000000';
                case 'green': return '#10b981';
                case 'red':
                default:
                    return '#ef4444';
            }
        }

        // Custom Glassmorphic Dropdown for PDF Mode Selection
        if (pdfModeWrapper) {
            const trigger = pdfModeWrapper.querySelector('.custom-select-trigger');
            const options = pdfModeWrapper.querySelectorAll('.custom-option');
            const input = document.getElementById('action-mode');
            const selectedSpan = document.getElementById('pdf-action-mode-selected');

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                pdfModeWrapper.classList.toggle('open');
            });

            document.addEventListener('click', function () {
                pdfModeWrapper.classList.remove('open');
            });

            options.forEach(opt => {
                opt.addEventListener('click', function () {
                    options.forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    const value = this.getAttribute('data-value');
                    input.value = value;
                    
                    const titleText = this.querySelector('strong').textContent;
                    const iconClass = value === 'split' ? 'bi-pencil-square' : 'bi-file-earmark-plus-fill';
                    const iconColor = value === 'split' ? 'var(--color-primary)' : 'var(--color-accent)';
                    
                    selectedSpan.innerHTML = `<i class="bi ${iconClass}" style="color: ${iconColor};"></i> ${titleText}`;
                    pdfModeWrapper.classList.remove('open');
                    
                    updateModeUI(value);
                });
            });
        }

        function updateModeUI(mode) {
            if (mode === 'split') {
                btnDownloadZip.innerHTML = '<i class="bi bi-file-earmark-pdf-fill"></i> Download Hasil Suntingan PDF';
                pageRangeHelp.textContent = 'Mode Sunting Lampiran PDF: Pilih, rotasi, pisahkan rentang halaman, atau beri stempel catatan teks pada dokumen PDF.';
            } else {
                btnDownloadZip.innerHTML = '<i class="bi bi-file-earmark-pdf-fill"></i> Download Hasil Penggabungan (PDF)';
                pageRangeHelp.textContent = 'Mode Merge: Seluruh halaman terpilih di bawah akan digabungkan menjadi 1 berkas PDF tunggal.';
            }
        }

        // Dropzone Click & Drag
        dropZone.addEventListener('click', () => pdfUpload.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--color-primary)';
            dropZone.style.background = 'var(--glass-highlight)';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = 'var(--glass-border)';
            dropZone.style.background = 'rgba(255,255,255,0.01)';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--glass-border)';
            dropZone.style.background = 'rgba(255,255,255,0.01)';
            if (e.dataTransfer.files.length) {
                handleFiles(e.dataTransfer.files);
            }
        });

        pdfUpload.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleFiles(e.target.files);
            }
        });

        btnRemove.addEventListener('click', () => {
            clearFiles();
        });

        function clearFiles() {
            loadedPdfFiles = [];
            selectedPagesMap.clear();
            rotationMap = {};
            customWatermarkPos = null;
            totalGlobalPages = 0;
            fileInfo.style.display = 'none';
            previewGrid.innerHTML = '';
            previewEmpty.style.display = 'flex';
            btnDownloadZip.disabled = true;
            btnSelectAll.disabled = true;
            btnDeselectAll.disabled = true;
            pageRangeInput.value = '';
            pdfUpload.value = '';
        }

        async function handleFiles(files) {
            clearFiles();
            if (!files || !files.length) return;

            const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20 MB limit in bytes

            const fileList = Array.from(files).filter(f => {
                const fname = (f.name || '').toLowerCase();
                const ftype = (f.type || '').toLowerCase();
                return ftype.includes('pdf') || fname.endsWith('.pdf') || fname.includes('.pdf') || files.length > 0;
            });

            if (!fileList.length) return;

            // Validate 20 MB max file size
            for (let file of fileList) {
                if (file.size > MAX_FILE_SIZE) {
                    alert(`Ukuran berkas "${file.name}" (${(file.size / (1024 * 1024)).toFixed(1)} MB) melebihi batas maksimum 20 MB! Silakan pilih berkas yang lebih kecil.`);
                    clearFiles();
                    return;
                }
            }

            // Check File Size and Smart Recommendation for > 5 MB
            checkFileSizesAndRecommend(fileList);

            fileName.textContent = fileList.length === 1 ? fileList[0].name : `${fileList.length} Berkas PDF Terpilih`;
            fileInfo.style.display = 'block';
            previewEmpty.style.display = 'none';

            previewGrid.innerHTML = `
                <div id="preview-loading" style="grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 50px 20px; color: var(--text-muted);">
                    <i class="bi bi-arrow-repeat spin" style="font-size: 40px; color: var(--color-primary); margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Memproses Pratinjau PDF (Maks. 20 MB)...</p>
                    <span style="font-size: 11px; color: var(--text-muted);">Sedang membaca dan membuat lembar cetak visual</span>
                </div>
            `;

            let globalPageCounter = 1;

            try {
                for (let fIndex = 0; fIndex < fileList.length; fIndex++) {
                    const file = fileList[fIndex];
                    const arrayBuffer = await file.arrayBuffer();
                    
                    // Clone ArrayBuffer to avoid detachment issues between pdf.js and pdf-lib
                    const buffer1 = arrayBuffer.slice(0);
                    const buffer2 = arrayBuffer.slice(0);

                    const loadingTask = pdfjsLib.getDocument({ data: new Uint8Array(buffer1) });
                    const pdfjsDoc = await loadingTask.promise;
                    const pdfLibDoc = await PDFLib.PDFDocument.load(buffer2, { ignoreEncryption: true });

                    loadedPdfFiles.push({
                        file: file,
                        pdfjsDoc: pdfjsDoc,
                        pdfLibDoc: pdfLibDoc,
                        totalPages: pdfjsDoc.numPages,
                        startIndex: globalPageCounter
                    });

                    const loadingEl = document.getElementById('preview-loading');
                    if (loadingEl) loadingEl.remove();

                    for (let pageNum = 1; pageNum <= pdfjsDoc.numPages; pageNum++) {
                        const currentIndex = globalPageCounter++;
                        selectedPagesMap.add(currentIndex);
                        await renderThumbnail(pdfjsDoc, pageNum, currentIndex);
                        // Yield thread to allow smooth visual rendering
                        await new Promise(r => setTimeout(r, 5));
                    }
                }

                totalGlobalPages = globalPageCounter - 1;
                filePagesCount.textContent = `Total ${totalGlobalPages} Halaman`;
                updatePageRangeInput();
                btnDownloadZip.disabled = false;
                btnSelectAll.disabled = false;
                btnDeselectAll.disabled = false;
            } catch (err) {
                console.error("PDF Processing Error:", err);
                alert('Gagal membaca berkas PDF: ' + (err.message || 'Format tidak valid'));
                clearFiles();
            }
        }

        async function renderThumbnail(pdfjsDoc, pageNum, globalIndex) {
            const page = await pdfjsDoc.getPage(pageNum);
            const viewport = page.getViewport({ scale: 0.3 });

            const itemCard = document.createElement('div');
            itemCard.className = 'thumb-card selected';
            itemCard.dataset.page = globalIndex;
            itemCard.style.cssText = `
                position: relative;
                background: rgba(255,255,255,0.03);
                border: 2px solid var(--color-primary);
                border-radius: var(--radius-sm);
                padding: 8px;
                display: flex;
                flex-direction: column;
                align-items: center;
                cursor: pointer;
                transition: var(--transition-smooth);
            `;

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            canvas.style.cssText = 'width: 100%; height: auto; border-radius: 4px; background: #fff; transition: transform 0.3s ease;';

            await page.render({ canvasContext: context, viewport: viewport }).promise;

            const badge = document.createElement('span');
            badge.textContent = `Hal ${globalIndex}`;
            badge.style.cssText = 'margin-top: 6px; font-size: 11px; font-weight: 600; color: var(--text-primary);';

            const checkMark = document.createElement('div');
            checkMark.className = 'checkmark';
            checkMark.innerHTML = '<i class="bi bi-check-circle-fill" style="color: var(--color-primary); font-size: 18px;"></i>';
            checkMark.style.cssText = 'position: absolute; top: 4px; right: 4px; background: var(--bg-dark); border-radius: 50%; display: flex;';

            itemCard.appendChild(checkMark);
            itemCard.appendChild(canvas);
            itemCard.appendChild(badge);

            itemCard.addEventListener('click', () => {
                if (selectedPagesMap.has(globalIndex)) {
                    selectedPagesMap.delete(globalIndex);
                    itemCard.style.borderColor = 'var(--glass-border)';
                    itemCard.style.opacity = '0.5';
                    checkMark.style.display = 'none';
                } else {
                    selectedPagesMap.add(globalIndex);
                    itemCard.style.borderColor = 'var(--color-primary)';
                    itemCard.style.opacity = '1';
                    checkMark.style.display = 'flex';
                }
                updatePageRangeInput();
            });

            previewGrid.appendChild(itemCard);
        }

        // FULLSCREEN HIGH DEFINITION INTERACTIVE MOUSE DRAG MODAL (HD SCALE 2.2 + DYNAMIC ZOOMING)
        if (btnOpenDragModal) {
            btnOpenDragModal.addEventListener('click', async () => {
                if (!loadedPdfFiles.length) {
                    alert('Silakan pilih atau unggah berkas PDF terlebih dahulu untuk mengatur posisi dan ukuran teks!');
                    return;
                }

                if (!watermarkCheckbox.checked) {
                    watermarkCheckbox.checked = true;
                    watermarkGroup.style.display = 'flex';
                }

                dragModal.style.display = 'flex';

                // Sync Modal Inputs with Left Panel Controls
                const currentText = watermarkInput.value.trim() || 'DISETUJUI EXIM';
                modalWatermarkInput.value = currentText;
                modalColorSelect.value = watermarkColorSelect.value;
                
                currentFontSize = parseInt(watermarkSizeSelect.value) || 14;
                modalFontSizeSelect.value = currentFontSize;
                currentDocumentZoom = 1.0;
                applyCanvasDocumentZoom();

                // Render page 1 on spacious drag modal canvas (High Definition scale 2.2 for ultra crisp clear preview)
                const pdfItem = loadedPdfFiles[0];
                const page = await pdfItem.pdfjsDoc.getPage(1);
                const viewport = page.getViewport({ scale: 2.2 });

                dragCanvas.height = viewport.height;
                dragCanvas.width = viewport.width;
                const ctx = dragCanvas.getContext('2d');
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                // Update text content, color, and size
                badgeDragTextContent.textContent = currentText;
                dragTextBadge.style.color = getWatermarkColorHex(modalColorSelect.value);
                dragTextBadge.style.fontSize = `${currentFontSize * 1.25}px`; // Scaled for HD preview
                dragTextBadge.style.display = 'block';

                // Initial position setup
                const initPos = customWatermarkPos || getPresetPositionPercent(watermarkPosSelect.value);
                tempDragPos = { ...initPos };

                updateBadgePositionFromPos(tempDragPos);
            });
        }

        // Document Zoom Handlers
        function applyCanvasDocumentZoom() {
            canvasZoomContainer.style.transform = `scale(${currentDocumentZoom})`;
            zoomPageLevel.textContent = `${Math.round(currentDocumentZoom * 100)}%`;
        }

        if (btnZoomInPage) {
            btnZoomInPage.addEventListener('click', () => {
                currentDocumentZoom = Math.min(2.5, currentDocumentZoom + 0.15);
                applyCanvasDocumentZoom();
            });
        }

        if (btnZoomOutPage) {
            btnZoomOutPage.addEventListener('click', () => {
                currentDocumentZoom = Math.max(0.6, currentDocumentZoom - 0.15);
                applyCanvasDocumentZoom();
            });
        }

        if (btnZoomResetPage) {
            btnZoomResetPage.addEventListener('click', () => {
                currentDocumentZoom = 1.0;
                applyCanvasDocumentZoom();
            });
        }

        // Modal Live Inputs Events (Text Edit, Color Change, Font Size Adjustment)
        if (modalWatermarkInput) {
            modalWatermarkInput.addEventListener('input', () => {
                const val = modalWatermarkInput.value.trim() || 'DISETUJUI EXIM';
                badgeDragTextContent.textContent = val;
                watermarkInput.value = modalWatermarkInput.value;
            });
        }

        if (modalColorSelect) {
            modalColorSelect.addEventListener('change', () => {
                const colorHex = getWatermarkColorHex(modalColorSelect.value);
                dragTextBadge.style.color = colorHex;
                watermarkColorSelect.value = modalColorSelect.value;
            });
        }

        if (modalFontSizeSelect) {
            modalFontSizeSelect.addEventListener('change', () => {
                currentFontSize = parseInt(modalFontSizeSelect.value) || 14;
                dragTextBadge.style.fontSize = `${currentFontSize * 1.25}px`;
                watermarkSizeSelect.value = currentFontSize;
                updateCoordInfo();
            });
        }

        if (btnFontSmaller) {
            btnFontSmaller.addEventListener('click', () => {
                currentFontSize = Math.max(8, currentFontSize - 2);
                modalFontSizeSelect.value = currentFontSize;
                watermarkSizeSelect.value = currentFontSize;
                dragTextBadge.style.fontSize = `${currentFontSize * 1.25}px`;
                updateCoordInfo();
            });
        }

        if (btnFontLarger) {
            btnFontLarger.addEventListener('click', () => {
                currentFontSize = Math.min(48, currentFontSize + 2);
                modalFontSizeSelect.value = currentFontSize;
                watermarkSizeSelect.value = currentFontSize;
                dragTextBadge.style.fontSize = `${currentFontSize * 1.25}px`;
                updateCoordInfo();
            });
        }

        function updateCoordInfo() {
            dragCoordInfo.textContent = `Posisi Teks: X: ${Math.round(tempDragPos.xPercent * 100)}%, Y: ${Math.round(tempDragPos.yPercent * 100)}% | Ukuran: ${currentFontSize}pt`;
        }

        function updateBadgePositionFromPos(pos) {
            setTimeout(() => {
                const cWidth = dragCanvas.width;
                const cHeight = dragCanvas.height;

                const leftPx = pos.xPercent * cWidth;
                const topPx = pos.yPercent * cHeight;

                dragTextBadge.style.left = `${leftPx}px`;
                dragTextBadge.style.top = `${topPx}px`;
                updateCoordInfo();
            }, 60);
        }

        // Drag Badge Mouse Events in Modal
        let isDraggingBadge = false;
        let startX, startY, startLeft, startTop;

        dragTextBadge.addEventListener('mousedown', (e) => {
            e.stopPropagation();
            e.preventDefault();
            isDraggingBadge = true;
            startX = e.clientX;
            startY = e.clientY;
            startLeft = dragTextBadge.offsetLeft;
            startTop = dragTextBadge.offsetTop;

            const onMouseMove = (mEvt) => {
                if (!isDraggingBadge) return;
                const deltaX = (mEvt.clientX - startX) / currentDocumentZoom;
                const deltaY = (mEvt.clientY - startY) / currentDocumentZoom;

                let newL = startLeft + deltaX;
                let newT = startTop + deltaY;

                const cWidth = dragCanvas.width;
                const cHeight = dragCanvas.height;
                const bWidth = dragTextBadge.offsetWidth;
                const bHeight = dragTextBadge.offsetHeight;

                newL = Math.max(0, Math.min(cWidth - bWidth, newL));
                newT = Math.max(0, Math.min(cHeight - bHeight, newT));

                dragTextBadge.style.left = `${newL}px`;
                dragTextBadge.style.top = `${newT}px`;

                const xPercent = newL / cWidth;
                const yPercent = newT / cHeight;
                tempDragPos = { xPercent, yPercent };
                updateCoordInfo();
            };

            const onMouseUp = () => {
                isDraggingBadge = false;
                window.removeEventListener('mousemove', onMouseMove);
                window.removeEventListener('mouseup', onMouseUp);
            };

            window.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);
        });

        if (btnSaveDragPos) {
            btnSaveDragPos.addEventListener('click', () => {
                customWatermarkPos = { ...tempDragPos };
                watermarkPosSelect.value = 'custom';
                watermarkInput.value = modalWatermarkInput.value;
                watermarkColorSelect.value = modalColorSelect.value;
                watermarkSizeSelect.value = currentFontSize;

                dragModal.style.display = 'none';
                alert(`Posisi dan ukuran teks (${currentFontSize}pt) berhasil disimpan! Perubahan akan diterapkan saat Anda mendownload PDF.`);
            });
        }

        if (btnCancelDragPos) {
            btnCancelDragPos.addEventListener('click', () => {
                dragModal.style.display = 'none';
            });
        }

        if (btnCloseDragModal) {
            btnCloseDragModal.addEventListener('click', () => {
                dragModal.style.display = 'none';
            });
        }

        function updatePageRangeInput() {
            const sorted = Array.from(selectedPagesMap).sort((a, b) => a - b);
            pageRangeInput.value = formatRange(sorted);
        }

        function formatRange(numbers) {
            if (!numbers.length) return '';
            let ranges = [];
            let start = numbers[0], end = numbers[0];

            for (let i = 1; i < numbers.length; i++) {
                if (numbers[i] === end + 1) {
                    end = numbers[i];
                } else {
                    ranges.push(start === end ? `${start}` : `${start}-${end}`);
                    start = end = numbers[i];
                }
            }
            ranges.push(start === end ? `${start}` : `${start}-${end}`);
            return ranges.join(', ');
        }

        function parsePageRangeInput(text, maxPage) {
            const pages = new Set();
            if (!text || !text.trim()) return pages;
            const parts = text.split(',');
            for (let part of parts) {
                part = part.trim();
                if (part.includes('-')) {
                    const [startStr, endStr] = part.split('-');
                    const start = parseInt(startStr);
                    const end = parseInt(endStr);
                    if (!isNaN(start) && !isNaN(end)) {
                        for (let i = Math.min(start, end); i <= Math.max(start, end); i++) {
                            if (i >= 1 && i <= maxPage) pages.add(i);
                        }
                    }
                } else {
                    const pageNum = parseInt(part);
                    if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= maxPage) {
                        pages.add(pageNum);
                    }
                }
            }
            return pages;
        }

        // Watermark Checkbox Toggle
        const watermarkGroup = document.getElementById('watermark-input-group');
        if (watermarkCheckbox && watermarkGroup) {
            watermarkCheckbox.addEventListener('change', () => {
                watermarkGroup.style.display = watermarkCheckbox.checked ? 'flex' : 'none';
            });
        }

        // Rotate Selected Pages Button
        const btnRotateSelected = document.getElementById('btn-rotate-selected');
        if (btnRotateSelected) {
            btnRotateSelected.addEventListener('click', () => {
                if (!selectedPagesMap.size) {
                    alert('Silakan pilih setidaknya 1 halaman PDF untuk dirotasi!');
                    return;
                }
                selectedPagesMap.forEach(globalIdx => {
                    rotationMap[globalIdx] = ((rotationMap[globalIdx] || 0) + 90) % 360;
                    const card = document.querySelector(`.thumb-card[data-page="${globalIdx}"]`);
                    if (card) {
                        const canvas = card.querySelector('canvas');
                        if (canvas) {
                            canvas.style.transform = `rotate(${rotationMap[globalIdx]}deg)`;
                        }
                    }
                });
            });
        }

        pageRangeInput.addEventListener('input', () => {
            if (!totalGlobalPages) return;
            const parsed = parsePageRangeInput(pageRangeInput.value, totalGlobalPages);
            selectedPagesMap = parsed;

            document.querySelectorAll('.thumb-card').forEach(card => {
                const idx = parseInt(card.dataset.page);
                const checkMark = card.querySelector('.checkmark');
                if (selectedPagesMap.has(idx)) {
                    card.style.borderColor = 'var(--color-primary)';
                    card.style.opacity = '1';
                    checkMark.style.display = 'flex';
                } else {
                    card.style.borderColor = 'var(--glass-border)';
                    card.style.opacity = '0.5';
                    checkMark.style.display = 'none';
                }
            });
        });

        btnSelectAll.addEventListener('click', () => {
            document.querySelectorAll('.thumb-card').forEach(card => {
                const idx = parseInt(card.dataset.page);
                selectedPagesMap.add(idx);
                card.style.borderColor = 'var(--color-primary)';
                card.style.opacity = '1';
                card.querySelector('.checkmark').style.display = 'flex';
            });
            updatePageRangeInput();
        });

        btnDeselectAll.addEventListener('click', () => {
            selectedPagesMap.clear();
            document.querySelectorAll('.thumb-card').forEach(card => {
                card.style.borderColor = 'var(--glass-border)';
                card.style.opacity = '0.5';
                card.querySelector('.checkmark').style.display = 'none';
            });
            updatePageRangeInput();
        });

        // Smart Recommendation Check for > 5 MB
        function checkFileSizesAndRecommend(fileList) {
            let totalBytes = 0;
            for (let f of fileList) totalBytes += f.size;

            const sizeInMB = (totalBytes / (1024 * 1024)).toFixed(1);
            const banner = document.getElementById('compression-recommendation');
            const text = document.getElementById('recommendation-text');
            const checkbox = document.getElementById('enable-compression');
            const badge = document.getElementById('compress-badge');

            if (totalBytes > 5 * 1024 * 1024) {
                checkbox.checked = true;
                banner.style.display = 'block';
                badge.style.display = 'inline-block';
                text.textContent = `Ukuran total berkas PDF yang diunggah adalah ${sizeInMB} MB (> 5 MB). Rekomendasi fitur kompresi diaktifkan secara otomatis agar ukuran berkas akhir tidak melebihi batas 5 MB.`;
            } else {
                banner.style.display = 'none';
                badge.style.display = 'none';
            }
        }

        // PDF Compression Helper (Target <= 5 MB)
        async function saveCompressedPdf(pdfDoc) {
            const isCompressEnabled = document.getElementById('enable-compression').checked;

            if (!isCompressEnabled) {
                return await pdfDoc.save({ useObjectStreams: true });
            }

            // Object Streams & Content Stream Optimization
            const compressedBytes = await pdfDoc.save({
                useObjectStreams: true,
                addDefaultPage: false,
            });

            // If still above 5 MB, apply canvas JPEG re-sampling
            if (compressedBytes.length > 5 * 1024 * 1024) {
                try {
                    const tempDoc = await PDFLib.PDFDocument.create();
                    const pdfjsDoc = await pdfjsLib.getDocument({ data: new Uint8Array(compressedBytes) }).promise;

                    for (let i = 1; i <= pdfjsDoc.numPages; i++) {
                        const page = await pdfjsDoc.getPage(i);
                        const viewport = page.getViewport({ scale: 1.0 });
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                        const jpegDataUrl = canvas.toDataURL('image/jpeg', 0.70);
                        const jpegImageBytes = await fetch(jpegDataUrl).then(res => res.arrayBuffer());
                        const jpegImage = await tempDoc.embedJpg(jpegImageBytes);

                        const newPage = tempDoc.addPage([viewport.width, viewport.height]);
                        newPage.drawImage(jpegImage, {
                            x: 0,
                            y: 0,
                            width: viewport.width,
                            height: viewport.height,
                        });
                    }

                    return await tempDoc.save({ useObjectStreams: true });
                } catch (e) {
                    console.warn("Canvas JPEG optimization fallback:", e);
                    return compressedBytes;
                }
            }

            return compressedBytes;
        }

        // Generate Custom PDF & Download Direct PDF File(s)
        btnDownloadZip.addEventListener('click', async () => {
            if (!selectedPagesMap.size) {
                alert('Silakan pilih setidaknya 1 halaman PDF untuk diproses!');
                return;
            }

            btnDownloadZip.disabled = true;
            btnDownloadZip.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Memproses PDF...';

            try {
                const selectedIndices = Array.from(selectedPagesMap).sort((a, b) => a - b);
                const currentMode = document.getElementById('action-mode').value;
                const userFileName = zipNameInput.value.trim() || 'Dokumen_EXIM_Sunting.pdf';
                const baseName = userFileName.replace(/\.pdf$/i, '').replace(/\.zip$/i, '');

                if (currentMode === 'split') {
                    // SUNTING LAMPIRAN PDF MODE: Generate and trigger direct .pdf download for each range group
                    const rangeGroups = formatRange(selectedIndices).split(', ').filter(Boolean);
                    
                    for (let i = 0; i < rangeGroups.length; i++) {
                        const r = rangeGroups[i];
                        const newPdfDoc = await PDFLib.PDFDocument.create();
                        const pageNums = Array.from(parsePageRangeInput(r, totalGlobalPages));

                        for (const globalIdx of pageNums) {
                            for (const pdfItem of loadedPdfFiles) {
                                const localIndex = globalIdx - pdfItem.startIndex;
                                if (localIndex >= 0 && localIndex < pdfItem.totalPages) {
                                    const [copiedPage] = await newPdfDoc.copyPages(pdfItem.pdfLibDoc, [localIndex]);
                                    
                                    // Apply Page Rotation if set
                                    const extraAngle = rotationMap[globalIdx] || 0;
                                    if (extraAngle !== 0) {
                                        const origAngle = copiedPage.getRotation().angle;
                                        copiedPage.setRotation(PDFLib.degrees((origAngle + extraAngle) % 360));
                                    }

                                    // Apply Stempel / Catatan Teks if enabled
                                    if (watermarkCheckbox && watermarkCheckbox.checked) {
                                        const wText = watermarkInput.value.trim();
                                        if (wText) {
                                            const { width, height } = copiedPage.getSize();
                                            const colorVal = watermarkColorSelect.value;
                                            const posVal = watermarkPosSelect.value;

                                            let textColor = PDFLib.rgb(0.9, 0.1, 0.1);
                                            if (colorVal === 'blue') textColor = PDFLib.rgb(0.1, 0.3, 0.9);
                                            else if (colorVal === 'black') textColor = PDFLib.rgb(0.1, 0.1, 0.1);
                                            else if (colorVal === 'green') textColor = PDFLib.rgb(0.1, 0.7, 0.2);

                                            const pos = (posVal === 'custom' && customWatermarkPos) ? customWatermarkPos : getPresetPositionPercent(posVal);

                                            const helveticaFont = await newPdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
                                            const fontSizeVal = parseInt(watermarkSizeSelect.value) || currentFontSize || 14;
                                            const textWidth = helveticaFont.widthOfTextAtSize(wText, fontSizeVal);

                                            let pdfX = pos.xPercent * width;
                                            if (posVal === 'top-right' && posVal !== 'custom') {
                                                pdfX = width - textWidth - 30; // Positioned right at the top right margin aligned with red mark line
                                            } else {
                                                pdfX = Math.max(15, Math.min(width - textWidth - 15, pdfX));
                                            }

                                            let pdfY = height - (pos.yPercent * height) - (fontSizeVal + 2);
                                            if (posVal === 'top-right' && posVal !== 'custom') {
                                                pdfY = height - fontSizeVal - 18; // Very top margin area aligned with red mark line
                                            } else {
                                                pdfY = Math.max(15, Math.min(height - (fontSizeVal + 10), pdfY));
                                            }

                                            copiedPage.drawText(wText, {
                                                x: pdfX,
                                                y: pdfY,
                                                size: fontSizeVal,
                                                font: helveticaFont,
                                                color: textColor,
                                            });
                                        }
                                    }

                                    newPdfDoc.addPage(copiedPage);
                                    break;
                                }
                            }
                        }

                        if (newPdfDoc.getPageCount() > 0) {
                            const pdfBytes = await saveCompressedPdf(newPdfDoc);
                            const pdfBlob = new Blob([pdfBytes], { type: 'application/pdf' });
                            const downloadUrl = URL.createObjectURL(pdfBlob);

                            const fileName = rangeGroups.length === 1 
                                ? `${baseName}.pdf` 
                                : `${baseName}_Lampiran_Hal_${r.replace(/-/g, '_sd_')}.pdf`;

                            const a = document.createElement('a');
                            a.href = downloadUrl;
                            a.download = fileName;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);

                            await new Promise(res => setTimeout(res, 300));
                            URL.revokeObjectURL(downloadUrl);
                        }
                    }
                } else {
                    // MERGE MODE: Combine all selected pages into 1 single PDF file and download directly
                    const newPdfDoc = await PDFLib.PDFDocument.create();
                    for (const globalIdx of selectedIndices) {
                        for (const pdfItem of loadedPdfFiles) {
                            const localIndex = globalIdx - pdfItem.startIndex;
                            if (localIndex >= 0 && localIndex < pdfItem.totalPages) {
                                const [copiedPage] = await newPdfDoc.copyPages(pdfItem.pdfLibDoc, [localIndex]);
                                
                                // Apply Page Rotation if set
                                const extraAngle = rotationMap[globalIdx] || 0;
                                if (extraAngle !== 0) {
                                    const origAngle = copiedPage.getRotation().angle;
                                    copiedPage.setRotation(PDFLib.degrees((origAngle + extraAngle) % 360));
                                }

                                // Apply Stempel / Catatan Teks if enabled
                                if (watermarkCheckbox && watermarkCheckbox.checked) {
                                    const wText = watermarkInput.value.trim();
                                    if (wText) {
                                        const { width, height } = copiedPage.getSize();
                                        const colorVal = watermarkColorSelect.value;
                                        const posVal = watermarkPosSelect.value;

                                        let textColor = PDFLib.rgb(0.9, 0.1, 0.1);
                                        if (colorVal === 'blue') textColor = PDFLib.rgb(0.1, 0.3, 0.9);
                                        else if (colorVal === 'black') textColor = PDFLib.rgb(0.1, 0.1, 0.1);
                                        else if (colorVal === 'green') textColor = PDFLib.rgb(0.1, 0.7, 0.2);

                                        const pos = (posVal === 'custom' && customWatermarkPos) ? customWatermarkPos : getPresetPositionPercent(posVal);

                                        const helveticaFont = await newPdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
                                        const fontSizeVal = parseInt(watermarkSizeSelect.value) || currentFontSize || 14;
                                        const textWidth = helveticaFont.widthOfTextAtSize(wText, fontSizeVal);

                                        let pdfX = pos.xPercent * width;
                                        if (posVal === 'top-right' && posVal !== 'custom') {
                                            pdfX = width - textWidth - 30; // Positioned right at the top right margin aligned with red mark line
                                        } else {
                                            pdfX = Math.max(15, Math.min(width - textWidth - 15, pdfX));
                                        }

                                        let pdfY = height - (pos.yPercent * height) - (fontSizeVal + 2);
                                        if (posVal === 'top-right' && posVal !== 'custom') {
                                            pdfY = height - fontSizeVal - 18; // Very top margin area aligned with red mark line
                                        } else {
                                            pdfY = Math.max(15, Math.min(height - (fontSizeVal + 10), pdfY));
                                        }

                                        copiedPage.drawText(wText, {
                                            x: pdfX,
                                            y: pdfY,
                                            size: fontSizeVal,
                                            font: helveticaFont,
                                            color: textColor,
                                        });
                                    }
                                }

                                newPdfDoc.addPage(copiedPage);
                                break;
                            }
                        }
                    }

                    if (newPdfDoc.getPageCount() > 0) {
                        const pdfBytes = await saveCompressedPdf(newPdfDoc);
                        const pdfBlob = new Blob([pdfBytes], { type: 'application/pdf' });
                        const downloadUrl = URL.createObjectURL(pdfBlob);

                        const finalPdfName = `${baseName}.pdf`;

                        const a = document.createElement('a');
                        a.href = downloadUrl;
                        a.download = finalPdfName;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        setTimeout(() => URL.revokeObjectURL(downloadUrl), 2000);
                    } else {
                        alert('Tidak ada halaman PDF valid yang dapat diproses.');
                    }
                }

            } catch (err) {
                console.error("Download Error:", err);
                alert('Terjadi kesalahan saat memproses berkas PDF: ' + err.message);
            } finally {
                btnDownloadZip.disabled = false;
                updateModeUI(document.getElementById('action-mode').value);
            }
        });
    });
</script>
@endsection
