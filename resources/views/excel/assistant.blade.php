@extends('layouts.app')

@section('title', 'AI Excel Assistant')
@section('header_title', 'AI Excel Assistant')
@section('header_subtitle', 'Asisten cerdas pendukung produktivitas dan pemecahan formula Excel laporan bulanan Anda')

@section('content')
<style>
    .custom-error-option {
        padding: 10px 14px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .custom-error-option:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary) !important;
    }
    .custom-error-option.active {
        background: rgba(99, 102, 241, 0.15) !important;
        color: var(--color-primary) !important;
        font-weight: 700;
    }
    #error-dropdown-btn:hover {
        border-color: var(--color-primary) !important;
        box-shadow: 0 0 10px rgba(99, 102, 241, 0.2);
    }
</style>
<div class="dashboard-columns" style="grid-template-columns: 280px 1fr; gap: 20px; align-items: start;">

    <!-- Left Tab Sidebar -->
    <div class="card" style="padding: 16px; display: flex; flex-direction: column; gap: 6px;">
        <h3 style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; padding: 0 8px 8px 8px; border-bottom: 1px solid var(--glass-border); margin-bottom: 8px;">Fitur Asisten AI</h3>
        
        <button class="tab-btn active" data-target="perbaikan" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; width: 100%; border: none; border-radius: var(--radius-sm); text-align: left; background: rgba(99,102,241,0.08); color: var(--color-primary); font-weight: 600; cursor: pointer; transition: var(--transition-smooth);">
            <i class="bi bi-wrench-adjustable text-success" style="font-size: 15px; color: var(--color-success) !important;"></i>
            <span style="font-size: 12px;">Perbaikan Formula</span>
        </button>

        <button class="tab-btn" data-target="membuat" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; width: 100%; border: none; border-radius: var(--radius-sm); text-align: left; background: transparent; color: var(--text-secondary); font-weight: 500; cursor: pointer; transition: var(--transition-smooth);">
            <i class="bi bi-magic text-accent" style="font-size: 15px; color: var(--color-accent) !important;"></i>
            <span style="font-size: 12px;">Buat Formula Baru</span>
        </button>

        <button class="tab-btn" data-target="menjelaskan" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; width: 100%; border: none; border-radius: var(--radius-sm); text-align: left; background: transparent; color: var(--text-secondary); font-weight: 500; cursor: pointer; transition: var(--transition-smooth);">
            <i class="bi bi-book-half text-warning" style="font-size: 15px; color: var(--color-warning) !important;"></i>
            <span style="font-size: 12px;">Menjelaskan Formula</span>
        </button>

        <button class="tab-btn" data-target="modern" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; width: 100%; border: none; border-radius: var(--radius-sm); text-align: left; background: transparent; color: var(--text-secondary); font-weight: 500; cursor: pointer; transition: var(--transition-smooth);">
            <i class="bi bi-rocket-takeoff-fill text-primary" style="font-size: 15px; color: var(--color-primary) !important;"></i>
            <span style="font-size: 12px;">Upgrade Formula Modern</span>
        </button>

        <button class="tab-btn" data-target="optimasi" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; width: 100%; border: none; border-radius: var(--radius-sm); text-align: left; background: transparent; color: var(--text-secondary); font-weight: 500; cursor: pointer; transition: var(--transition-smooth);">
            <i class="bi bi-lightning-charge-fill text-success" style="font-size: 15px; color: var(--color-success) !important;"></i>
            <span style="font-size: 12px;">Optimasi Formula</span>
        </button>

        <button class="tab-btn" data-target="error" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; width: 100%; border: none; border-radius: var(--radius-sm); text-align: left; background: transparent; color: var(--text-secondary); font-weight: 500; cursor: pointer; transition: var(--transition-smooth);">
            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 15px; color: var(--color-danger) !important;"></i>
            <span style="font-size: 12px;">Analisis Error Workbook</span>
        </button>

        <button class="tab-btn" data-target="belajar" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; width: 100%; border: none; border-radius: var(--radius-sm); text-align: left; background: transparent; color: var(--text-secondary); font-weight: 500; cursor: pointer; transition: var(--transition-smooth);">
            <i class="bi bi-mortarboard-fill text-info" style="font-size: 15px; color: var(--color-info) !important;"></i>
            <span style="font-size: 12px;">AI Belajar Excel</span>
        </button>
    </div>

    <!-- Right Workspace Area -->
    <div class="card" style="padding: 24px; min-height: 480px; display: flex; flex-direction: column; justify-content: space-between;">
        
        <!-- Workspace Forms Container -->
        <div id="workspace-content" style="flex-grow: 1;">
            
            <!-- Tab 1: Perbaikan Formula -->
            <div class="tab-pane active" id="pane-perbaikan">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;"><i class="bi bi-wrench-adjustable text-success" style="color: var(--color-success) !important;"></i> Perbaikan Formula Excel</h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Temukan kesalahan penulisan (*syntax error*), koma/titik koma, atau tanda kurung yang hilang di rumus Anda.</p>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label" style="font-size: 11px;">Tempel Formula Bermasalah</label>
                    <textarea class="form-control" id="fix-formula-input" rows="3" placeholder="Contoh: =VLOOKUP(A2, B:C, 3, FALSE)" style="font-family: monospace; font-size: 13px; padding: 12px;"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-size: 11px;">Jelaskan Error atau Hasil yang Diharapkan (Opsional)</label>
                    <input type="text" class="form-control" id="fix-error-desc" placeholder="Contoh: Mengembalikan error #REF! atau salah indeks kolom" style="font-size: 12px; padding: 10px 14px;">
                </div>
                <button type="button" class="btn btn-success btn-process" data-action="perbaikan" style="padding: 10px 20px; font-size: 12px;"><i class="bi bi-lightning-fill"></i> Perbaiki Formula via AI</button>
            </div>

            <!-- Tab 2: Buat Formula Baru -->
            <div class="tab-pane" id="pane-membuat" style="display: none;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;"><i class="bi bi-magic text-accent" style="color: var(--color-accent) !important;"></i> Buat Formula Baru</h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Jelaskan dalam bahasa sehari-hari apa yang ingin Anda hitung atau cari, dan AI akan merumuskannya untuk Anda.</p>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-size: 11px;">Deskripsikan Rumus yang Anda Inginkan</label>
                    <textarea class="form-control" id="create-formula-input" rows="4" placeholder="Contoh: Saya ingin mencari nilai dari sel A2 di kolom B Sheet2, lalu mengambil nilai yang sejajar di kolom E. Jika tidak ditemukan, tampilkan teks 'Tidak Ada'." style="font-size: 12px; padding: 12px; line-height: 1.5;"></textarea>
                </div>
                <button type="button" class="btn btn-primary btn-process" data-action="membuat" style="padding: 10px 20px; font-size: 12px;"><i class="bi bi-cpu-fill"></i> Dapatkan Rekomendasi Formula</button>
            </div>

            <!-- Tab 3: Menjelaskan Formula -->
            <div class="tab-pane" id="pane-menjelaskan" style="display: none;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;"><i class="bi bi-book-half text-warning" style="color: var(--color-warning) !important;"></i> Jelaskan Langkah demi Langkah</h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Tempel rumus Excel yang rumit atau panjang di sini, dan asisten AI akan menguraikan cara kerjanya secara mendalam.</p>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-size: 11px;">Tempel Formula yang Ingin Dijelaskan</label>
                    <textarea class="form-control" id="explain-formula-input" rows="4" placeholder="Contoh: =SUMPRODUCT((A2:A100=&quot;Impor&quot;)*(B2:B100&gt;2026)*C2:C100)" style="font-family: monospace; font-size: 13px; padding: 12px;"></textarea>
                </div>
                <button type="button" class="btn btn-warning btn-process" data-action="menjelaskan" style="padding: 10px 20px; font-size: 12px; color: #1e1b4b;"><i class="bi bi-search"></i> Jelaskan Rumus Ini</button>
            </div>

            <!-- Tab 4: Upgrade Formula Modern -->
            <div class="tab-pane" id="pane-modern" style="display: none;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;"><i class="bi bi-rocket-takeoff-fill text-primary" style="color: var(--color-primary) !important;"></i> Upgrade ke Formula Modern</h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Ubah formula Excel versi lama (seperti VLOOKUP lambat atau IF bercabang panjang) menjadi versi Office 365 yang modern dan efisien (seperti XLOOKUP, IFS, SWITCH, atau LET).</p>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-size: 11px;">Tempel Formula Lama Anda</label>
                    <textarea class="form-control" id="modern-formula-input" rows="4" placeholder="Contoh: =IF(A2=1, &quot;Senin&quot;, IF(A2=2, &quot;Selasa&quot;, IF(A2=3, &quot;Rabu&quot;, &quot;Hari Lain&quot;)))" style="font-family: monospace; font-size: 13px; padding: 12px;"></textarea>
                </div>
                <button type="button" class="btn btn-primary btn-process" data-action="modern" style="padding: 10px 20px; font-size: 12px;"><i class="bi bi-stars"></i> Upgrade Formula Sekarang</button>
            </div>

            <!-- Tab 5: Optimasi Formula -->
            <div class="tab-pane" id="pane-optimasi" style="display: none;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;"><i class="bi bi-lightning-charge-fill text-success" style="color: var(--color-success) !important;"></i> Optimasi Kecepatan Formula</h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Dapatkan versi formula yang dioptimalkan untuk mengurangi beban kalkulasi dan mempercepat performa workbook laporan Anda.</p>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-size: 11px;">Tempel Formula yang Lambat / Berat</label>
                    <textarea class="form-control" id="optimize-formula-input" rows="4" placeholder="Contoh: =IF(ISERROR(VLOOKUP(A2, B:C, 2, FALSE)), &quot;&quot;, VLOOKUP(A2, B:C, 2, FALSE))" style="font-family: monospace; font-size: 13px; padding: 12px;"></textarea>
                </div>
                <button type="button" class="btn btn-success btn-process" data-action="optimasi" style="padding: 10px 20px; font-size: 12px;"><i class="bi bi-speedometer2"></i> Optimalkan Performa</button>
            </div>

            <!-- Tab 6: Error Workbook -->
            <div class="tab-pane" id="pane-error" style="display: none;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;"><i class="bi bi-exclamation-triangle-fill text-danger" style="color: var(--color-danger) !important;"></i> Analisis Excel Error Workbook</h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Temukan penyebab mengapa sel Excel Anda mengeluarkan kode error tertentu dan bagaimana langkah penyelesaiannya.</p>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label" style="font-size: 11px;">Pilih Jenis Error Excel</label>
                    <div class="custom-error-dropdown" style="position: relative; width: 100%;">
                        <button type="button" class="form-control" id="error-dropdown-btn" style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); color: var(--text-primary); cursor: pointer; text-align: left; width: 100%; transition: var(--transition-smooth);">
                            <span id="selected-error-display" data-value="#N/A">❌ #N/A (Data tidak ditemukan / Not Available)</span>
                            <i class="bi bi-chevron-down" style="font-size: 10px; opacity: 0.8;"></i>
                        </button>
                        <input type="hidden" id="error-type-select" value="#N/A">
                        <div class="custom-error-menu" id="error-dropdown-menu" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: rgba(15, 23, 42, 0.95); border: 1px solid var(--glass-border); border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5); z-index: 100; overflow: hidden; backdrop-filter: blur(8px); padding: 4px 0;">
                            <div class="custom-error-option active" data-value="#N/A">
                                ❌ #N/A (Data tidak ditemukan / Not Available)
                            </div>
                            <div class="custom-error-option" data-value="#VALUE!">
                                ⚠️ #VALUE! (Salah tipe argumen data)
                            </div>
                            <div class="custom-error-option" data-value="#REF!">
                                🔍 #REF! (Referensi sel terhapus / Invalid Reference)
                            </div>
                            <div class="custom-error-option" data-value="#DIV/0!">
                                ➗ #DIV/0! (Pembagian dengan nol)
                            </div>
                            <div class="custom-error-option" data-value="#NUM!">
                                🔢 #NUM! (Nilai numerik tidak valid)
                            </div>
                            <div class="custom-error-option" data-value="#NAME?">
                                ✍️ #NAME? (Salah ejaan rumus / nama range tidak ada)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-size: 11px;">Deskripsikan Konteks Rumus di Sel Tersebut</label>
                    <input type="text" class="form-control" id="error-context-desc" placeholder="Contoh: Rumus saya menggunakan INDEX MATCH di tabel database PIB" style="font-size: 12px; padding: 10px 14px;">
                </div>
                <button type="button" class="btn btn-danger btn-process" data-action="error" style="padding: 10px 20px; font-size: 12px;"><i class="bi bi-bug-fill"></i> Analisis Error & Cari Solusi</button>
            </div>

            <!-- Tab 7: AI Belajar Excel -->
            <div class="tab-pane" id="pane-belajar" style="display: none;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;"><i class="bi bi-mortarboard-fill text-info" style="color: var(--color-info) !important;"></i> AI Belajar Excel</h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Tanyakan apa saja seputar Excel: cara membuat Pivot Table, trik shortcuts, cara kerja VBA / Macros, atau formula tertentu.</p>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-size: 11px;">Tulis Pertanyaan Excel Anda</label>
                    <textarea class="form-control" id="learn-excel-input" rows="4" placeholder="Contoh: Bagaimana cara membuat ranking data otomatis di Excel tanpa merubah urutan baris?" style="font-size: 12px; padding: 12px; line-height: 1.5;"></textarea>
                </div>
                <button type="button" class="btn btn-info btn-process" data-action="belajar" style="padding: 10px 20px; font-size: 12px; color: #1e1b4b;"><i class="bi bi-chat-left-dots-fill"></i> Tanyakan Asisten AI</button>
            </div>

        </div>

        <!-- AI Output Panel (Dynamic Result) -->
        <div id="ai-output-container" style="display: none; border-top: 1px solid var(--glass-border); padding-top: 20px; margin-top: 20px; flex-grow: 1;">
            
            <!-- Loading Indicator -->
            <div id="ai-loading-wrapper" style="display: none; text-align: center; padding: 30px;">
                <div class="stat-icon spin" style="width: 48px; height: 48px; border-radius: 50%; background: rgba(99, 102, 241, 0.12); color: var(--color-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto; font-size: 22px;">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <p style="font-size: 12px; font-weight: 600; color: var(--text-primary);" id="loading-text-status">AI sedang merumuskan jawaban terbaik...</p>
                <div style="width: 150px; height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px; overflow: hidden; margin: 8px auto 0 auto; border: 1px solid var(--glass-border);">
                    <div style="width: 100%; height: 100%; background: var(--color-primary); animation: loadingProgress 1.5s infinite ease-in-out;"></div>
                </div>
            </div>

            <!-- Response Content -->
            <div id="ai-response-wrapper" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 11px; font-weight: 700; color: var(--color-accent); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-cpu-fill"></i> REKOMENDASI ASISTEN AI
                    </span>
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-copy-response" style="font-size: 11px; padding: 4px 10px;"><i class="bi bi-clipboard"></i> Salin Jawaban</button>
                </div>
                <div style="background: rgba(0,0,0,0.18); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); padding: 16px; min-height: 150px; max-height: 380px; overflow-y: auto;">
                    <div id="ai-response-body" style="font-size: 12px; color: var(--text-primary); line-height: 1.6;"></div>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    @keyframes loadingProgress {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Custom Error Dropdown Toggle
        const errorDropdownBtn = document.getElementById('error-dropdown-btn');
        const errorDropdownMenu = document.getElementById('error-dropdown-menu');
        const errorTypeSelectHidden = document.getElementById('error-type-select');
        const selectedErrorDisplay = document.getElementById('selected-error-display');

        if (errorDropdownBtn && errorDropdownMenu) {
            errorDropdownBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (errorDropdownMenu.style.display === 'block') {
                    errorDropdownMenu.style.display = 'none';
                } else {
                    errorDropdownMenu.style.display = 'block';
                }
            });

            // Handle options
            document.querySelectorAll('.custom-error-option').forEach(option => {
                option.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const val = this.getAttribute('data-value');
                    const text = this.innerHTML.trim();

                    // Update hidden input & display
                    errorTypeSelectHidden.value = val;
                    selectedErrorDisplay.innerHTML = text;
                    selectedErrorDisplay.setAttribute('data-value', val);

                    // Update active classes
                    document.querySelectorAll('.custom-error-option').forEach(opt => {
                        opt.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Close menu
                    errorDropdownMenu.style.display = 'none';
                });
            });

            // Close on click outside
            window.addEventListener('click', function () {
                errorDropdownMenu.style.display = 'none';
            });
        }

        const tabs = document.querySelectorAll('.tab-btn');
        const panes = document.querySelectorAll('.tab-pane');
        
        const outputContainer = document.getElementById('ai-output-container');
        const loadingWrapper = document.getElementById('ai-loading-wrapper');
        const responseWrapper = document.getElementById('ai-response-wrapper');
        const responseBody = document.getElementById('ai-response-body');
        const btnCopy = document.getElementById('btn-copy-response');

        // Handles left sidebar tab changes
        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                // Clear active states on tabs
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.style.background = 'transparent';
                    t.style.color = 'var(--text-secondary)';
                    t.style.fontWeight = '500';
                });

                // Set active style on clicked tab
                this.classList.add('active');
                this.style.background = 'rgba(99, 102, 241, 0.08)';
                this.style.color = 'var(--color-primary)';
                this.style.fontWeight = '600';

                // Display targeted content pane
                const target = this.getAttribute('data-target');
                panes.forEach(pane => {
                    if (pane.id === `pane-${target}`) {
                        pane.style.display = 'block';
                    } else {
                        pane.style.display = 'none';
                    }
                });

                // Collapse previous AI output
                outputContainer.style.display = 'none';
                loadingWrapper.style.display = 'none';
                responseWrapper.style.display = 'none';
            });
        });

        // Trigger AI analysis simulation
        document.querySelectorAll('.btn-process').forEach(btn => {
            btn.addEventListener('click', function () {
                const action = this.getAttribute('data-action');
                
                // Read input based on selected tab action
                let userInput = '';
                let extraInput = '';
                
                if (action === 'perbaikan') {
                    userInput = document.getElementById('fix-formula-input').value.trim();
                    extraInput = document.getElementById('fix-error-desc').value.trim();
                } else if (action === 'membuat') {
                    userInput = document.getElementById('create-formula-input').value.trim();
                } else if (action === 'menjelaskan') {
                    userInput = document.getElementById('explain-formula-input').value.trim();
                } else if (action === 'modern') {
                    userInput = document.getElementById('modern-formula-input').value.trim();
                } else if (action === 'optimasi') {
                    userInput = document.getElementById('optimize-formula-input').value.trim();
                } else if (action === 'error') {
                    userInput = document.getElementById('error-type-select').value;
                    extraInput = document.getElementById('error-context-desc').value.trim();
                } else if (action === 'belajar') {
                    userInput = document.getElementById('learn-excel-input').value.trim();
                }

                if (!userInput) {
                    alert('Harap isi input formulir terlebih dahulu!');
                    return;
                }

                // Show loading layout
                outputContainer.style.display = 'block';
                loadingWrapper.style.display = 'block';
                responseWrapper.style.display = 'none';

                // Simulate AI response generation
                setTimeout(() => {
                    const generatedHTML = generateMockAIResult(action, userInput, extraInput);
                    loadingWrapper.style.display = 'none';
                    responseWrapper.style.display = 'block';
                    
                    // Simple typewriter effect simulation
                    responseBody.innerHTML = '';
                    let i = 0;
                    responseBody.innerHTML = generatedHTML; // Show compiled HTML content directly
                }, 1500);
            });
        });

        // Copy button trigger
        btnCopy.addEventListener('click', function () {
            // Copy pure text of responseBody
            const textToCopy = responseBody.innerText;
            navigator.clipboard.writeText(textToCopy).then(() => {
                const prevHtml = btnCopy.innerHTML;
                btnCopy.innerHTML = '<i class="bi bi-check-lg"></i> Berhasil Disalin';
                btnCopy.classList.remove('btn-secondary');
                btnCopy.classList.add('btn-success');
                
                setTimeout(() => {
                    btnCopy.innerHTML = prevHtml;
                    btnCopy.classList.remove('btn-success');
                    btnCopy.classList.add('btn-secondary');
                }, 1500);
            });
        });

        // AI Mock Response Logic Database Parser
        function generateMockAIResult(action, input, extra) {
            const inputLower = input.toLowerCase();
            
            // 1. Repair Formula
            if (action === 'perbaikan') {
                if (inputLower.includes('vlookup') && (inputLower.includes('3') || inputLower.includes('4'))) {
                    return `
                        <div style="color: #34d399; font-weight: bold; margin-bottom: 8px;">✔️ Rumus Berhasil Diperbaiki!</div>
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 8px 12px; font-family: monospace; font-size: 13px; color: var(--color-success); border-radius: var(--radius-sm); margin-bottom: 12px;">
                            =VLOOKUP(A2, B:D, 3, FALSE)
                        </div>
                        <strong>Analisis Error:</strong><br>
                        Formula awal Anda <code style="font-family: monospace; background: rgba(255,255,255,0.08); padding: 2px 4px; border-radius: 3px;">=VLOOKUP(A2, B:C, 3, FALSE)</code> memicu error <code style="color: var(--color-danger);">#REF!</code> karena rentang kolom pencarian Anda hanya <code style="font-family: monospace;">B:C</code> (terdiri dari 2 kolom), tetapi indeks pencarian kolom yang Anda masukkan bernilai <strong>3</strong>.<br><br>
                        <strong>Langkah Solusi:</strong><br>
                        1. Rentang kolom diperlebar menjadi <code style="font-family: monospace;">B:D</code> agar menampung 3 kolom.<br>
                        2. Indeks kolom diset ke 3 untuk mengambil data di kolom D.
                    `;
                }
                
                return `
                    <div style="color: #34d399; font-weight: bold; margin-bottom: 8px;">✔️ Formula Berhasil Diperbaiki!</div>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 8px 12px; font-family: monospace; font-size: 13px; color: var(--color-success); border-radius: var(--radius-sm); margin-bottom: 12px;">
                        =IFERROR(${input.startsWith('=') ? input.slice(1) : input}, "")
                    </div>
                    <strong>Analisis Masalah:</strong><br>
                    Formula Anda berhasil distandarisasi untuk mencegah luapan kode error. Kami membungkus formula Anda menggunakan fungsi penjinak error <code style="font-family: monospace;">IFERROR</code>.<br><br>
                    <strong>Penjelasan:</strong><br>
                    Jika sel menghasilkan kesalahan hitung seperti #N/A atau #VALUE!, Excel otomatis mengembalikan sel kosong <code style="font-family: monospace;">""</code> agar tampilan tabel laporan bulanan EXIM Anda tetap rapi.
                `;
            }

            // 2. Suggest New Formula
            if (action === 'membuat') {
                if (inputLower.includes('cari') || inputLower.includes('vlookup') || inputLower.includes('ambil')) {
                    return `
                        <strong>Rekomendasi Rumus Utama:</strong><br>
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 8px 12px; font-family: monospace; font-size: 13px; color: var(--color-success); border-radius: var(--radius-sm); margin-bottom: 12px;">
                            =XLOOKUP(A2, Sheet2!B:B, Sheet2!E:E, "Tidak Ada", 0)
                        </div>
                        <strong>Cara Kerja Formula:</strong><br>
                        <ul>
                            <li><code style="font-family: monospace;">A2</code>: Nilai acuan yang ingin dicari.</li>
                            <li><code style="font-family: monospace;">Sheet2!B:B</code>: Kolom pencarian di Sheet tujuan.</li>
                            <li><code style="font-family: monospace;">Sheet2!E:E</code>: Kolom hasil yang ingin diambil nilainya.</li>
                            <li><code style="font-family: monospace;">"Tidak Ada"</code>: Nilai alternatif jika data tidak ditemukan.</li>
                        </ul>
                        <br>
                        <strong>Alternatif Formula (Office Lama):</strong><br>
                        <code style="font-family: monospace; color: var(--text-muted);">=IFERROR(INDEX(Sheet2!E:E, MATCH(A2, Sheet2!B:B, 0)), "Tidak Ada")</code>
                    `;
                }

                return `
                    <strong>Rekomendasi Formula:</strong><br>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 8px 12px; font-family: monospace; font-size: 13px; color: var(--color-success); border-radius: var(--radius-sm); margin-bottom: 12px;">
                        =SUMIFS(C:C, A:A, "Impor", B:B, "Januari")
                    </div>
                    <strong>Penjelasan Struktur Rumus:</strong><br>
                    Formula menggunakan <code style="font-family: monospace;">SUMIFS</code> untuk menjumlahkan nilai pada Kolom C berdasarkan dua kriteria: Nilai kolom A harus bernilai "Impor" dan Nilai kolom B harus bernilai "Januari".
                `;
            }

            // 3. Explain Formula
            if (action === 'menjelaskan') {
                if (inputLower.includes('sumproduct')) {
                    return `
                        <strong>Uraian Logika SUMPRODUCT:</strong><br>
                        Rumus awal: <code style="font-family: monospace; background: rgba(255,255,255,0.08); padding: 2px 4px; border-radius: 3px;">${input}</code><br><br>
                        <strong>Langkah Eksekusi Rumus oleh Excel:</strong><br>
                        1. <code style="font-family: monospace;">(A2:A100="Impor")</code>: Mengecek baris kolom A dari baris 2 hingga 100. Menghasilkan array logika TRUE/FALSE.<br>
                        2. <code style="font-family: monospace;">(B2:B100>2026)</code>: Mengecek baris kolom B yang memiliki nilai di atas tahun 2026. Menghasilkan array TRUE/FALSE.<br>
                        3. Perhitungan <code style="font-family: monospace;">*</code> (perkalian) mengubah array TRUE menjadi 1 dan FALSE menjadi 0.<br>
                        4. Hasil perkalian logika tersebut dikalikan dengan baris nilai di kolom <code style="font-family: monospace;">C2:C100</code>.<br>
                        5. Terakhir, <code style="font-family: monospace;">SUMPRODUCT</code> menjumlahkan hasil perkalian tersebut untuk mendapatkan total akhir.
                    `;
                }

                return `
                    <strong>Struktur Penjelasan Formula:</strong><br>
                    Rumus: <code style="font-family: monospace; background: rgba(255,255,255,0.08); padding: 2px 4px; border-radius: 3px;">${input}</code><br><br>
                    <strong>Langkah Pengecekan:</strong><br>
                    Formula memproses data array atau baris sel secara bertingkat dari sel yang ditentukan. Kriteria pencarian dicocokkan satu demi satu sebelum akhirnya mengeluarkan hasil kalkulasi numerik/teks akhir.
                `;
            }

            // 4. Upgrade Formula Modern
            if (action === 'modern') {
                if (inputLower.includes('if(')) {
                    return `
                        <strong>Hasil Upgrade ke Rumus Modern (Office 365):</strong><br>
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 8px 12px; font-family: monospace; font-size: 13px; color: var(--color-success); border-radius: var(--radius-sm); margin-bottom: 12px;">
                            =SWITCH(A2, 1, "Senin", 2, "Selasa", 3, "Rabu", "Hari Lain")
                        </div>
                        <strong>Mengapa Rumus Ini Lebih Baik?</strong><br>
                        <ul>
                            <li><strong>Lebih Ringkas</strong>: Menghilangkan tanda kurung tutup bertumpuk di akhir rumus.</li>
                            <li><strong>Lebih Cepat Dibaca</strong>: Mengevaluasi satu sel acuan secara berurutan dan mengembalikan nilai yang cocok.</li>
                        </ul>
                    `;
                }

                return `
                    <strong>Hasil Upgrade Formula Modern:</strong><br>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 8px 12px; font-family: monospace; font-size: 13px; color: var(--color-success); border-radius: var(--radius-sm); margin-bottom: 12px;">
                        =XLOOKUP(A2, B:B, C:C, "Tidak Ada")
                    </div>
                    <strong>Keuntungan XLOOKUP dibanding VLOOKUP:</strong><br>
                    Tidak terikat nomor indeks kolom, mendukung pencarian ke kiri secara fleksibel, dan memiliki parameter error-handling terintegrasi tanpa perlu dibungkus IFERROR kembali.
                `;
            }

            // 5. Optimasi Formula
            if (action === 'optimasi') {
                return `
                    <strong>Rekomendasi Formula Teroptimasi:</strong><br>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 8px 12px; font-family: monospace; font-size: 13px; color: var(--color-success); border-radius: var(--radius-sm); margin-bottom: 12px;">
                        =IFERROR(VLOOKUP(A2, B:C, 2, FALSE), "")
                    </div>
                    <strong>Analisis Optimasi:</strong><br>
                    Pada rumus lama Anda, Excel dipaksa menjalankan fungsi <code style="font-family: monospace;">VLOOKUP</code> sebanyak dua kali untuk mengecek error saja. Pada rumus baru yang kami optimalkan, VLOOKUP hanya dieksekusi **satu kali** di dalam <code style="font-family: monospace;">IFERROR</code>, memotong waktu eksekusi workbook hingga 50%!
                `;
            }

            // 6. Error Workbook
            if (action === 'error') {
                if (input === '#N/A') {
                    return `
                        <strong>Penyebab Error #N/A (Data Not Found):</strong><br>
                        Error terjadi karena pencarian VLOOKUP/INDEX-MATCH tidak dapat menemukan data yang cocok dengan sel acuan di tabel database tujuan.<br><br>
                        <strong>Langkah Perbaikan Mandiri:</strong><br>
                        1. Pastikan ejaan data acuan di tabel asal sama persis dengan tabel tujuan.<br>
                        2. Cek apakah ada spasi tersembunyi. Anda bisa menggunakan rumus pembersih: <code style="font-family: monospace;">=TRIM(A2)</code>.<br>
                        3. Bungkus rumus menggunakan penjinak error: <code style="font-family: monospace;">=IFERROR(RUMUS_ANDA, "Tidak Ditemukan")</code>.
                    `;
                }

                return `
                    <strong>Analisis Error Excel ${input}:</strong><br>
                    Error ini biasanya disebabkan oleh kesalahan referensi sel, format data yang tidak cocok (misalnya menjumlahkan teks dengan angka), atau kolom rujukan yang telah terhapus.<br><br>
                    <strong>Rekomendasi Solusi:</strong><br>
                    Pastikan sel yang Anda rujuk di ${extra || 'rumus Anda'} bertipe data yang sama (angka dikalikan dengan angka) dan rentang sheet tidak rusak.
                `;
            }

            // 7. AI Belajar Excel
            if (action === 'belajar') {
                if (inputLower.includes('pivot')) {
                    return `
                        <strong>Panduan Membuat Pivot Table Excel:</strong><br>
                        Pivot Table sangat berguna untuk meringkas laporan ribuan baris dokumen menjadi ringkasan yang rapi.<br><br>
                        <strong>Langkah Membuatnya:</strong><br>
                        1. Blok seluruh tabel data Anda.<br>
                        2. Pilih menu <strong>Insert</strong> pada toolbar atas, lalu klik <strong>PivotTable</strong>.<br>
                        3. Pilih tujuan peletakan (New Worksheet / Existing Worksheet). Klik OK.<br>
                        4. Di panel kanan, seret kolom kriteria ke area <strong>Rows</strong> (Baris) dan seret kolom nominal yang ingin dijumlahkan ke area <strong>Values</strong> (Nilai).
                    `;
                }
                
                if (inputLower.includes('xlookup')) {
                    return `
                        <strong>Panduan Belajar Fungsi XLOOKUP:</strong><br>
                        XLOOKUP adalah penerus modern VLOOKUP dan HLOOKUP di Office 365.<br><br>
                        <strong>Sintaks Rumus:</strong><br>
                        <code style="font-family: monospace;">=XLOOKUP(nilai_dicari, kolom_pencarian, kolom_hasil, [jika_tidak_ada], [mode_pencocokan])</code><br><br>
                        <strong>Kelebihan XLOOKUP:</strong><br>
                        - Bisa mencari data ke arah kiri.<br>
                        - Tidak perlu menghitung jumlah kolom.<br>
                        - Pencarian default-nya adalah pencocokan persis (*exact match*), jadi tidak perlu mengetik FALSE di akhir rumus.
                    `;
                }

                return `
                    <strong>Jawaban Edukasi AI Excel:</strong><br>
                    Pertanyaan: <em>"${input}"</em><br><br>
                    Untuk menyelesaikan hal ini di Excel, Anda bisa memanfaatkan kombinasi rumus bersyarat (seperti <code style="font-family: monospace;">IF</code>, <code style="font-family: monospace;">COUNTIF</code>, atau <code style="font-family: monospace;">INDEX MATCH</code>) atau fitur bawaan Excel seperti Conditional Formatting.<br><br>
                    Silakan rincikan rumus atau nama kolom Anda agar kami dapat menuliskan kode formula yang siap Anda pakai secara langsung.
                `;
            }

            return '';
        }
    });
</script>
@endsection
