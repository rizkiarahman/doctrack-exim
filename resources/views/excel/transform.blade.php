@extends('layouts.app')

@section('title', 'Transform Excel')
@section('header_title', 'Transform & Merger Excel')
@section('header_subtitle', 'Gabungkan banyak berkas Excel PIB/PEB atau laporan bulanan sejenis secara otomatis menjadi satu file tunggal')

@section('content')
<!-- Include SheetJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<div class="dashboard-columns" style="grid-template-columns: 1fr 1fr; gap: 20px;">

    <!-- Left Panel: Dropzone & Upload Controls -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div class="card" style="padding: 24px; position: relative; overflow: hidden;">
            <div style="position: absolute; width: 150px; height: 150px; background: radial-gradient(circle, rgba(16,185,129,0.08) 0%, transparent 70%); top: -50px; right: -50px; pointer-events: none;"></div>
            
            <h3 class="section-title" style="font-size: 16px; margin-bottom: 14px; display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-file-earmark-excel-fill text-success" style="color: var(--color-success) !important; font-size: 20px;"></i>
                Unggah Berkas Excel
            </h3>
            
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 18px; line-height: 1.5;">
                Silakan pilih atau seret 4-5 berkas Excel (.xlsx / .xls) laporan bulanan Anda. Pastikan semua file memiliki nama sheet dan struktur kolom yang sama.
            </p>

            <div id="excel-drop-zone" style="border: 2px dashed var(--glass-border); border-radius: var(--radius-md); padding: 45px 20px; text-align: center; cursor: pointer; background: rgba(255,255,255,0.01); transition: var(--transition-smooth);">
                <i class="bi bi-file-earmark-arrow-up-fill text-success" style="font-size: 45px; color: var(--color-success); display: block; margin-bottom: 12px;"></i>
                <p style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Pilih atau Tarik File Excel ke Sini</p>
                <span style="font-size: 11px; color: var(--text-muted); display: block; margin-bottom: 10px;">Format: .xlsx, .xls (Maks. 15 MB per file)</span>
                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 6px 16px; pointer-events: none;">Cari Berkas...</button>
                <input type="file" id="excel-upload" accept=".xlsx, .xls" multiple style="display: none;">
            </div>

            <!-- Smart Guidelines Alert -->
            <div style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15); border-radius: var(--radius-sm); padding: 12px 14px; margin-top: 20px; display: flex; gap: 10px;">
                <i class="bi bi-lightbulb-fill text-success" style="font-size: 16px; color: var(--color-success); margin-top: 2px;"></i>
                <div style="font-size: 11px; color: var(--text-secondary); line-height: 1.4;">
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 2px;">Aturan Penggabungan Otomatis</strong>
                    Data file 2 sampai 5 akan digabungkan di bawah baris data file ke-1. Header baris pertama pada file ke 2-5 akan dilewati secara otomatis untuk mencegah duplikasi baris judul kolom.
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Uploaded Files & Merger Output -->
    <div class="card" style="padding: 24px; display: flex; flex-direction: column; min-height: 480px;">
        <h3 class="section-title" style="font-size: 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span><i class="bi bi-list-task text-primary" style="color: var(--color-primary);"></i> Daftar Berkas Terpilih</span>
            <button type="button" id="btn-clear-excel" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px;" disabled><i class="bi bi-trash"></i> Bersihkan Semua</button>
        </h3>

        <!-- Empty State -->
        <div id="excel-empty-state" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); text-align: center; padding: 40px;">
            <i class="bi bi-file-spreadsheet" style="font-size: 55px; opacity: 0.25; margin-bottom: 12px; color: var(--color-success);"></i>
            <p style="font-size: 13px; font-weight: 500;">Belum Ada File Excel yang Terpilih</p>
            <span style="font-size: 11px; opacity: 0.7;">Silakan pilih minimal 2 file Excel laporan di sebelah kiri untuk memulai penggabungan.</span>
        </div>

        <!-- Files List Container -->
        <div id="excel-files-container" style="display: none; flex: 1; flex-direction: column; justify-content: space-between; gap: 15px;">
            <div style="overflow-y: auto; max-height: 280px; border: 1px solid var(--glass-border); border-radius: var(--radius-sm); background: rgba(0,0,0,0.15);">
                <table class="data-table" style="margin: 0; width: 100%; font-size: 12px;">
                    <thead>
                        <tr>
                            <th style="padding: 10px; width: 40px; text-align: center;">No</th>
                            <th style="padding: 10px;">Nama Berkas</th>
                            <th style="padding: 10px; width: 90px; text-align: right;">Ukuran</th>
                            <th style="padding: 10px; width: 120px; text-align: center;">Lembar (Sheet)</th>
                            <th style="padding: 10px; width: 60px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="excel-files-tbody">
                        <!-- Dynamic list rows go here -->
                    </tbody>
                </table>
            </div>

            <!-- Merging Actions Section -->
            <div style="border-top: 1px solid var(--glass-border); padding-top: 15px; display: flex; flex-direction: column; gap: 12px;">
                <!-- Live Progress Indicator -->
                <div id="merge-progress-wrapper" style="display: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px; font-size: 11px;">
                        <span id="merge-progress-status" style="color: var(--text-primary); font-weight: 500;">Menggabungkan lembar Excel...</span>
                        <span id="merge-progress-percent" style="color: var(--color-success); font-weight: 600;">0%</span>
                    </div>
                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden; border: 1px solid var(--glass-border);">
                        <div id="merge-progress-bar" style="width: 0%; height: 100%; background: var(--color-success); transition: width 0.15s ease;"></div>
                    </div>
                </div>

                <!-- Alert Message -->
                <div id="merge-alert-msg" style="display: none; padding: 10px; border-radius: var(--radius-sm); font-size: 11px; line-height: 1.4;"></div>

                <!-- Action Controls Buttons -->
                <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 12px; margin-top: 5px;">
                    <button type="button" id="btn-merge-process" class="btn btn-primary" style="justify-content: center; padding: 12px;">
                        <i class="bi bi-arrow-clockwise"></i> Refresh & Gabungkan Data
                    </button>
                    <button type="button" id="btn-download-merged" class="btn btn-success" style="justify-content: center; padding: 12px;" disabled>
                        <i class="bi bi-cloud-arrow-down-fill"></i> Download Laporan Bulanan
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('excel-drop-zone');
        const fileInput = document.getElementById('excel-upload');
        const btnClear = document.getElementById('btn-clear-excel');
        const emptyState = document.getElementById('excel-empty-state');
        const filesContainer = document.getElementById('excel-files-container');
        const tbody = document.getElementById('excel-files-tbody');
        
        const btnMergeProcess = document.getElementById('btn-merge-process');
        const btnDownloadMerged = document.getElementById('btn-download-merged');
        
        const progressWrapper = document.getElementById('merge-progress-wrapper');
        const progressPercent = document.getElementById('merge-progress-percent');
        const progressBar = document.getElementById('merge-progress-bar');
        const progressStatus = document.getElementById('merge-progress-status');
        
        const alertMsg = document.getElementById('merge-alert-msg');

        let selectedExcelFiles = []; // Array of File objects with extra metadata
        let mergedWorkbookResult = null; // Master merged workbook instance

        // Trigger file browser click
        dropZone.addEventListener('click', () => fileInput.click());

        // File drag and drop visual highlights
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--color-success)';
            dropZone.style.background = 'rgba(16, 185, 129, 0.04)';
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
                addExcelFiles(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                addExcelFiles(e.target.files);
            }
        });

        btnClear.addEventListener('click', () => {
            clearAllFiles();
        });

        function clearAllFiles() {
            selectedExcelFiles = [];
            mergedWorkbookResult = null;
            tbody.innerHTML = '';
            emptyState.style.display = 'flex';
            filesContainer.style.display = 'none';
            btnClear.disabled = true;
            btnDownloadMerged.disabled = true;
            btnDownloadMerged.innerHTML = '<i class="bi bi-cloud-arrow-down-fill"></i> Download Laporan Bulanan';
            progressWrapper.style.display = 'none';
            alertMsg.style.display = 'none';
            fileInput.value = '';
        }

        async function addExcelFiles(fileList) {
            const files = Array.from(fileList).filter(file => {
                const name = file.name.toLowerCase();
                return name.endsWith('.xlsx') || name.endsWith('.xls');
            });

            if (!files.length) return;

            const MAX_SIZE = 15 * 1024 * 1024; // 15 MB limit

            for (let file of files) {
                if (file.size > MAX_SIZE) {
                    alert(`Berkas "${file.name}" (${(file.size / (1024 * 1024)).toFixed(1)} MB) melebihi batas ukuran maksimum 15 MB!`);
                    continue;
                }

                // Avoid duplicate files
                if (selectedExcelFiles.some(f => f.name === file.name && f.size === file.size)) {
                    continue;
                }

                // Add loading metadata
                selectedExcelFiles.push({
                    file: file,
                    name: file.name,
                    size: file.size,
                    sheets: []
                });
            }

            if (selectedExcelFiles.length > 0) {
                emptyState.style.display = 'none';
                filesContainer.style.display = 'flex';
                btnClear.disabled = false;
                await renderFilesTable();
            }
        }

        async function renderFilesTable() {
            tbody.innerHTML = '';
            
            for (let i = 0; i < selectedExcelFiles.length; i++) {
                const fileObj = selectedExcelFiles[i];
                const row = document.createElement('tr');
                
                // Format file size
                const sizeKB = (fileObj.size / 1024).toFixed(1);
                const sizeFormatted = sizeKB > 1000 ? `${(sizeKB / 1024).toFixed(1)} MB` : `${sizeKB} KB`;

                row.innerHTML = `
                    <td style="text-align: center; vertical-align: middle; padding: 12px 10px;">${i + 1}</td>
                    <td style="font-weight: 500; color: var(--text-primary); vertical-align: middle; padding: 12px 10px;">
                        <i class="bi bi-file-earmark-spreadsheet text-success" style="margin-right: 6px;"></i> ${fileObj.name}
                    </td>
                    <td style="text-align: right; vertical-align: middle; padding: 12px 10px; color: var(--text-secondary);">${sizeFormatted}</td>
                    <td style="text-align: center; vertical-align: middle; padding: 12px 10px;" id="sheet-cell-${i}">
                        <i class="bi bi-arrow-repeat spin text-muted" style="font-size: 11px;"></i> Membaca...
                    </td>
                    <td style="text-align: center; vertical-align: middle; padding: 12px 10px;">
                        <button type="button" class="btn btn-secondary btn-sm btn-delete-file" data-index="${i}" style="padding: 4px 8px;" title="Hapus file"><i class="bi bi-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(row);

                // Fetch sheet names asynchronously in background
                if (!fileObj.sheets.length) {
                    readSheetsInBackground(fileObj, i);
                } else {
                    updateSheetCell(i, fileObj.sheets);
                }
            }

            // Bind Delete File buttons
            document.querySelectorAll('.btn-delete-file').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.getAttribute('data-index'));
                    selectedExcelFiles.splice(idx, 1);
                    if (selectedExcelFiles.length === 0) {
                        clearAllFiles();
                    } else {
                        renderFilesTable();
                        btnDownloadMerged.disabled = true;
                    }
                });
            });
        }

        function readSheetsInBackground(fileObj, index) {
            const reader = new FileReader();
            reader.onload = function (e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { bookSheets: true });
                    fileObj.sheets = workbook.SheetNames || [];
                    updateSheetCell(index, fileObj.sheets);
                } catch (err) {
                    console.error("Error reading sheets:", err);
                    updateSheetCellError(index);
                }
            };
            reader.onerror = () => updateSheetCellError(index);
            reader.readAsArrayBuffer(fileObj.file);
        }

        function updateSheetCell(index, sheets) {
            const cell = document.getElementById(`sheet-cell-${index}`);
            if (cell) {
                if (sheets.length === 1) {
                    cell.innerHTML = `<span class="badge badge-info" style="font-size: 10px;">${sheets[0]}</span>`;
                } else {
                    cell.innerHTML = `<span class="badge badge-primary" style="font-size: 10px;" title="${sheets.join(', ')}">${sheets.length} Sheets</span>`;
                }
            }
        }

        function updateSheetCellError(index) {
            const cell = document.getElementById(`sheet-cell-${index}`);
            if (cell) {
                cell.innerHTML = `<span class="badge badge-danger" style="font-size: 10px;">Format Error</span>`;
            }
        }

        // REFRESH & GABUNGKAN DATA AUTOMATIC PROCESSOR
        btnMergeProcess.addEventListener('click', async () => {
            if (selectedExcelFiles.length < 2) {
                alert('Silakan unggah minimal 2 berkas Excel untuk digabungkan!');
                return;
            }

            btnMergeProcess.disabled = true;
            btnMergeProcess.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Memproses Penggabungan...';
            btnDownloadMerged.disabled = true;
            
            progressWrapper.style.display = 'block';
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';
            progressStatus.textContent = 'Mulai membaca berkas...';
            
            alertMsg.style.display = 'none';

            try {
                // Step 1: Parse all files
                let parsedFiles = [];
                for (let i = 0; i < selectedExcelFiles.length; i++) {
                    const fileObj = selectedExcelFiles[i];
                    progressStatus.textContent = `Membaca berkas (${i + 1}/${selectedExcelFiles.length}): ${fileObj.name}...`;
                    progressBar.style.width = `${Math.round(((i + 1) / (selectedExcelFiles.length * 2)) * 100)}%`;
                    progressPercent.textContent = `${Math.round(((i + 1) / (selectedExcelFiles.length * 2)) * 100)}%`;

                    const arrayBuffer = await fileObj.file.arrayBuffer();
                    const workbook = XLSX.read(new Uint8Array(arrayBuffer), { type: 'array' });
                    parsedFiles.push({
                        name: fileObj.name,
                        workbook: workbook
                    });
                    await new Promise(r => setTimeout(r, 100)); // Smooth UI transition
                }

                // Step 2: Merge sheets
                progressStatus.textContent = 'Menggabungkan baris data secara cerdas...';
                
                const masterWb = XLSX.utils.book_new();
                const firstWorkbook = parsedFiles[0].workbook;
                const sheetNames = firstWorkbook.SheetNames;

                let rowCountsSummary = [];

                sheetNames.forEach((sheetName, sIdx) => {
                    let masterDataRows = [];
                    let sheetHeader = null;

                    parsedFiles.forEach((fileObj, fIdx) => {
                        const ws = fileObj.workbook.Sheets[sheetName];
                        if (!ws) {
                            console.warn(`Sheet "${sheetName}" tidak ditemukan di berkas ${fileObj.name}`);
                            return;
                        }

                        // Parse worksheet into array of arrays (AOA)
                        const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: "" });
                        if (rows.length === 0) return;

                        if (fIdx === 0) {
                            // Keep header row
                            sheetHeader = rows[0];
                            masterDataRows = masterDataRows.concat(rows);
                        } else {
                            // Append rows skipping the header row
                            const dataRows = rows.slice(1);
                            masterDataRows = masterDataRows.concat(dataRows);
                        }
                    });

                    if (masterDataRows.length > 0) {
                        const newWs = XLSX.utils.aoa_to_sheet(masterDataRows);
                        XLSX.utils.book_append_sheet(masterWb, newWs, sheetName);
                        rowCountsSummary.push(`Sheet "${sheetName}": ${masterDataRows.length - 1} baris data`);
                    }
                });

                progressBar.style.width = '90%';
                progressPercent.textContent = '90%';
                await new Promise(r => setTimeout(r, 200));

                mergedWorkbookResult = masterWb;

                // Finished Successfully
                progressBar.style.width = '100%';
                progressPercent.textContent = '100%';
                progressStatus.textContent = 'Penggabungan berhasil diselesaikan!';

                // Display success alert
                alertMsg.style.display = 'block';
                alertMsg.style.background = 'rgba(16, 185, 129, 0.08)';
                alertMsg.style.border = '1px solid rgba(16, 185, 129, 0.25)';
                alertMsg.style.color = '#34d399';
                alertMsg.innerHTML = `
                    <div style="display: flex; gap: 8px; align-items: flex-start;">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Berhasil Digabungkan!</strong>
                            <p style="margin: 2px 0 0 0; font-size: 10px; color: var(--text-muted);">
                                Total ${parsedFiles.length} file berhasil digabungkan.<br>
                                ${rowCountsSummary.join('<br>')}
                            </p>
                        </div>
                    </div>
                `;

                btnDownloadMerged.disabled = false;
                btnDownloadMerged.innerHTML = '<i class="bi bi-cloud-arrow-down-fill"></i> Download Laporan Bulanan';

            } catch (err) {
                console.error(err);
                progressBar.style.width = '100%';
                progressBar.style.backgroundColor = 'var(--color-danger)';
                progressPercent.textContent = 'Error';
                progressStatus.textContent = 'Gagal menggabungkan berkas Excel';

                // Display error alert
                alertMsg.style.display = 'block';
                alertMsg.style.background = 'rgba(239, 68, 68, 0.08)';
                alertMsg.style.border = '1px solid rgba(239, 68, 68, 0.25)';
                alertMsg.style.color = '#f87171';
                alertMsg.innerHTML = `
                    <div style="display: flex; gap: 8px; align-items: flex-start;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Gagal Menggabungkan!</strong>
                            <p style="margin: 2px 0 0 0; font-size: 10px;">${err.message || 'Pastikan struktur file Excel valid.'}</p>
                        </div>
                    </div>
                `;
            } finally {
                btnMergeProcess.disabled = false;
                btnMergeProcess.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh & Gabungkan Data';
            }
        });

        // TRIGGER DOWNLOAD COMBINED EXCEL FILE
        btnDownloadMerged.addEventListener('click', () => {
            if (!mergedWorkbookResult) return;
            try {
                // Generate date-based report name
                const dateObj = new Date();
                const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                const reportName = `Laporan_Bulanan_Gabungan_${monthNames[dateObj.getMonth()]}_${dateObj.getFullYear()}.xlsx`;

                XLSX.writeFile(mergedWorkbookResult, reportName);
            } catch (err) {
                alert('Gagal mengunduh file Excel: ' + err.message);
            }
        });
    });
</script>
@endsection
