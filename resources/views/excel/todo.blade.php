@extends('layouts.app')

@section('title', 'Daftar Tugas')
@section('header_title', 'Daftar Tugas Harian')
@section('header_subtitle', 'Pantau progress harian, atur waktu pengerjaan, dan update status aktivitas EXIM Anda')

@section('content')
<style>
    .custom-status-option {
        padding: 8px 14px;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .custom-status-option:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary) !important;
    }
    .custom-status-option.active {
        background: rgba(99, 102, 241, 0.15) !important;
        color: var(--color-primary) !important;
        font-weight: 700;
    }
    .custom-status-btn:hover {
        filter: brightness(1.15);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .custom-status-btn:active {
        transform: translateY(0);
    }
</style>
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Top Stats & Progress Banner -->
    <div class="card" style="padding: 24px; position: relative; overflow: hidden; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; align-items: center;">
        <div style="position: absolute; width: 200px; height: 200px; background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 70%); top: -50px; left: -50px; pointer-events: none;"></div>
        
        <!-- Left: Today's Status -->
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <span class="badge badge-primary" style="padding: 6px 12px; font-size: 11px;">Aktivitas Hari Ini</span>
                <span id="live-date-string" style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Membaca Tanggal...</span>
            </div>
            <h2 id="live-clock" style="font-size: 32px; font-weight: 800; font-family: var(--font-heading); color: var(--text-primary); margin: 0; letter-spacing: -0.5px;">00:00:00</h2>
            
            <!-- Progress Bar -->
            <div style="margin-top: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; margin-bottom: 5px;">
                    <span style="color: var(--text-muted); font-weight: 500;">Progress Kerja Hari Ini</span>
                    <span id="progress-text" style="color: var(--color-success); font-weight: 700;">{{ $progress }}% Selesai</span>
                </div>
                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden; border: 1px solid var(--glass-border);">
                    <div id="progress-bar-fill" style="width: {{ $progress }}%; height: 100%; background: linear-gradient(90deg, var(--color-primary), var(--color-success)); transition: width 0.4s ease-all;"></div>
                </div>
            </div>
        </div>

        <!-- Middle: Dynamic Active Task Info -->
        <div style="border-left: 1px solid var(--glass-border); padding-left: 20px;">
            <span style="font-size: 11px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 6px;"><i class="bi bi-play-circle-fill text-primary" style="color: var(--color-primary);"></i> TUGAS SEKARANG:</span>
            <p id="current-task-title" style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 36px;">
                Filing prepare dokumen untuk scan Beksis
            </p>
            <span id="current-task-time" style="font-size: 11px; color: var(--text-muted); font-weight: 500; display: block; margin-top: 4px;">08:00 - 09:00</span>
        </div>

        <!-- Right: Tasks Quick Status Counts -->
        <div style="border-left: 1px solid var(--glass-border); padding-left: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div style="text-align: center; background: rgba(255,255,255,0.01); padding: 8px; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                <span style="font-size: 18px; font-weight: 700; color: var(--color-success); display: block;" id="count-selesai">{{ $tasks->where('status', 'selesai')->count() }}</span>
                <span style="font-size: 10px; color: var(--text-muted);">Selesai</span>
            </div>
            <div style="text-align: center; background: rgba(255,255,255,0.01); padding: 8px; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                <span style="font-size: 18px; font-weight: 700; color: var(--color-primary); display: block;" id="count-sedang">{{ $tasks->where('status', 'sedang_dikerjakan')->count() }}</span>
                <span style="font-size: 10px; color: var(--text-muted);">Dikerjakan</span>
            </div>
            <div style="text-align: center; background: rgba(255,255,255,0.01); padding: 8px; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                <span style="font-size: 18px; font-weight: 700; color: var(--color-warning); display: block;" id="count-menunggu">{{ $tasks->where('status', 'menunggu')->count() }}</span>
                <span style="font-size: 10px; color: var(--text-muted);">Menunggu</span>
            </div>
            <div style="text-align: center; background: rgba(255,255,255,0.01); padding: 8px; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                <span style="font-size: 18px; font-weight: 700; color: var(--color-danger); display: block;" id="count-terlambat">{{ $tasks->where('status', 'terlambat')->count() }}</span>
                <span style="font-size: 10px; color: var(--text-muted);">Terlambat</span>
            </div>
        </div>
    </div>

    <!-- Active Timer Card & Timeline Layout -->
    <div class="dashboard-columns" style="grid-template-columns: 360px 1fr; gap: 20px;">

        <!-- Left: Stopwatch/Countdown Timer Widget -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="card" style="padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                <h4 style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-stopwatch text-accent" style="color: var(--color-accent);"></i> Timer Fokus Tugas
                </h4>

                <!-- Stopwatch Circle Display -->
                <div style="position: relative; width: 180px; height: 180px; border-radius: 50%; border: 6px solid var(--glass-border); display: flex; align-items: center; justify-content: center; margin-bottom: 15px; background: rgba(0,0,0,0.1); box-shadow: inset 0 4px 15px rgba(0,0,0,0.5);">
                    <div style="position: absolute; inset: -4px; border: 4px solid var(--color-primary); border-radius: 50%; border-right-color: transparent; border-bottom-color: transparent; transform: rotate(0deg); transition: transform 0.5s linear;" id="timer-ring"></div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <span id="timer-display" style="font-size: 30px; font-weight: 800; font-family: monospace; color: var(--text-primary); letter-spacing: -0.5px;">25:00</span>
                        <span id="timer-label" style="font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-top: 4px;">Fokus Kerja</span>
                    </div>
                </div>

                <!-- Timer Controls -->
                <div style="display: flex; gap: 10px; width: 100%; margin-bottom: 15px;">
                    <button type="button" id="btn-timer-start" class="btn btn-primary btn-sm" style="flex: 1; justify-content: center; gap: 4px; font-size: 12px; padding: 8px;"><i class="bi bi-play-fill"></i> Mulai</button>
                    <button type="button" id="btn-timer-pause" class="btn btn-secondary btn-sm" style="flex: 1; justify-content: center; gap: 4px; font-size: 12px; padding: 8px;" disabled><i class="bi bi-pause-fill"></i> Jeda</button>
                    <button type="button" id="btn-timer-reset" class="btn btn-secondary btn-sm" style="padding: 8px 12px; font-size: 12px;" title="Reset Timer"><i class="bi bi-arrow-counterclockwise"></i></button>
                </div>

                <!-- Fast Preset Pomodoro & Timer Modes -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; width: 100%;">
                    <button type="button" class="btn btn-secondary btn-sm timer-preset-btn" data-minutes="25" style="font-size: 11px; padding: 6px; justify-content: center;">25 Menit (Fokus)</button>
                    <button type="button" class="btn btn-secondary btn-sm timer-preset-btn" data-minutes="5" style="font-size: 11px; padding: 6px; justify-content: center;">5 Menit (Rehat)</button>
                    <button type="button" class="btn btn-secondary btn-sm timer-preset-btn" data-minutes="10" style="font-size: 11px; padding: 6px; justify-content: center;">10 Menit</button>
                    <button type="button" class="btn btn-secondary btn-sm timer-preset-btn" data-minutes="60" style="font-size: 11px; padding: 6px; justify-content: center;">1 Jam (Draft)</button>
                </div>
            </div>

            <!-- Custom Add Task Card -->
            <div class="card" style="padding: 20px;">
                <h4 style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-plus-circle text-success" style="color: var(--color-success);"></i> Tambah Tugas Custom
                </h4>
                <form action="{{ route('todo.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="text" name="title" class="form-control" placeholder="Nama tugas..." style="font-size: 12px; padding: 8px 12px;" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <input type="text" name="time_start" class="form-control" placeholder="Mulai (09:00)" style="font-size: 11px; padding: 8px 10px;" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <input type="text" name="time_end" class="form-control" placeholder="Selesai (10:00)" style="font-size: 11px; padding: 8px 10px;" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; padding: 8px;"><i class="bi bi-plus-lg"></i> Tambahkan</button>
                </form>
            </div>
        </div>

        <!-- Right: Tasks Cards & Timeline Grid -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="card" style="padding: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px;">
                    <h3 class="section-title" style="font-size: 15px; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-clock-history text-accent" style="color: var(--color-accent);"></i> Timeline Aktivitas Kerja
                    </h3>
                    <form action="{{ route('todo.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin me-reset daftar tugas hari ini ke jadwal awal?');">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px;"><i class="bi bi-arrow-counterclockwise"></i> Reset Jadwal</button>
                    </form>
                </div>

                <!-- Tasks Cards Grid -->
                <div style="display: grid; grid-template-columns: 1fr; gap: 12px;" id="tasks-list-grid">
                    @forelse($tasks as $idx => $task)
                        @php
                            // Determine status border/color classes
                            $statusColor = 'grey';
                            $statusLabel = 'Belum Mulai';
                            if ($task->status === 'sedang_dikerjakan') {
                                $statusColor = 'var(--color-primary)';
                                $statusLabel = 'Sedang Dikerjakan';
                            } elseif ($task->status === 'menunggu') {
                                $statusColor = 'var(--color-warning)';
                                $statusLabel = 'Menunggu';
                            } elseif ($task->status === 'selesai') {
                                $statusColor = 'var(--color-success)';
                                $statusLabel = 'Selesai';
                            } elseif ($task->status === 'terlambat') {
                                $statusColor = 'var(--color-danger)';
                                $statusLabel = 'Terlambat';
                            }
                        @endphp
                        
                        <div class="task-card" data-id="{{ $task->id }}" data-start="{{ $task->time_start }}" data-end="{{ $task->time_end }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-radius: var(--radius-md); border: 1px solid var(--glass-border); border-left: 5px solid {{ $statusColor }}; background: rgba(255,255,255,0.015); transition: var(--transition-smooth);">
                            <div style="display: flex; align-items: center; gap: 15px; overflow: hidden; flex: 1;">
                                <!-- Time Badge -->
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 85px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 6px 10px; border-radius: var(--radius-sm);">
                                    <span style="font-size: 11px; font-weight: 700; color: var(--text-primary);">{{ $task->time_start }}</span>
                                    <span style="font-size: 10px; color: var(--text-muted); margin-top: 2px;">s/d {{ $task->time_end }}</span>
                                </div>
                                
                                <!-- Task Details -->
                                <div style="overflow: hidden; padding-right: 15px;">
                                    <h4 style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.4; display: flex; align-items: center; gap: 8px;">
                                        {{ $task->title }}
                                        <span class="active-indicator" style="display: none; width: 8px; height: 8px; border-radius: 50%; background: #6366f1; box-shadow: 0 0 10px #6366f1;"></span>
                                    </h4>
                                    @if($task->notes)
                                        <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">Catatan: {{ $task->notes }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Controls: Status Switchers -->
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <!-- Custom Styled Status Select -->
                                @php
                                    $bgStyle = 'rgba(148, 163, 184, 0.12)';
                                    $borderStyle = 'rgba(148, 163, 184, 0.25)';
                                    $statusText = 'Belum Mulai';
                                    $statusEmoji = '⚪';
                                    if ($task->status === 'sedang_dikerjakan') {
                                        $bgStyle = 'rgba(14, 165, 233, 0.12)';
                                        $borderStyle = 'rgba(14, 165, 233, 0.35)';
                                        $statusText = 'Dikerjakan';
                                        $statusEmoji = '🔵';
                                    } elseif ($task->status === 'menunggu') {
                                        $bgStyle = 'rgba(245, 158, 11, 0.12)';
                                        $borderStyle = 'rgba(245, 158, 11, 0.35)';
                                        $statusText = 'Menunggu';
                                        $statusEmoji = '🟠';
                                    } elseif ($task->status === 'selesai') {
                                        $bgStyle = 'rgba(16, 185, 129, 0.12)';
                                        $borderStyle = 'rgba(16, 185, 129, 0.35)';
                                        $statusText = 'Selesai';
                                        $statusEmoji = '🟢';
                                    } elseif ($task->status === 'terlambat') {
                                        $bgStyle = 'rgba(239, 68, 68, 0.12)';
                                        $borderStyle = 'rgba(239, 68, 68, 0.35)';
                                        $statusText = 'Terlambat';
                                        $statusEmoji = '🔴';
                                    }
                                @endphp
                                <div class="custom-status-dropdown" style="position: relative; width: 140px; z-index: 10;">
                                    <button type="button" class="custom-status-btn" data-id="{{ $task->id }}" data-current-status="{{ $task->status }}" style="display: flex; align-items: center; justify-content: space-between; gap: 6px; font-size: 11px; font-weight: 600; padding: 7px 12px; width: 100%; border-radius: 8px; border: 1px solid {{ $borderStyle }}; background: {{ $bgStyle }}; color: var(--text-primary); transition: all 0.2s ease; cursor: pointer; text-align: left;">
                                        <span class="status-btn-content" style="display: flex; align-items: center; gap: 5px;">
                                            <span>{{ $statusEmoji }}</span>
                                            <span>{{ $statusText }}</span>
                                        </span>
                                        <i class="bi bi-chevron-down" style="font-size: 9px; opacity: 0.7;"></i>
                                    </button>
                                    <div class="custom-status-menu" id="status-menu-{{ $task->id }}" style="display: none; position: absolute; top: calc(100% + 6px); right: 0; background: rgba(15, 23, 42, 0.95); border: 1px solid var(--glass-border); border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5); z-index: 100; min-width: 140px; overflow: hidden; backdrop-filter: blur(8px); padding: 4px 0;">
                                        <div class="custom-status-option {{ $task->status === 'belum_mulai' ? 'active' : '' }}" data-value="belum_mulai">
                                            ⚪ Belum Mulai
                                        </div>
                                        <div class="custom-status-option {{ $task->status === 'sedang_dikerjakan' ? 'active' : '' }}" data-value="sedang_dikerjakan">
                                            🔵 Dikerjakan
                                        </div>
                                        <div class="custom-status-option {{ $task->status === 'menunggu' ? 'active' : '' }}" data-value="menunggu">
                                            🟠 Menunggu
                                        </div>
                                        <div class="custom-status-option {{ $task->status === 'selesai' ? 'active' : '' }}" data-value="selesai">
                                            🟢 Selesai
                                        </div>
                                        <div class="custom-status-option {{ $task->status === 'terlambat' ? 'active' : '' }}" data-value="terlambat">
                                            🔴 Terlambat
                                        </div>
                                    </div>
                                </div>

                                @if($task->time_start !== '08:00' && $task->time_start !== '09:00' && $task->time_start !== '10:00' && $task->time_start !== '10:30' && $task->time_start !== '13:00' && $task->time_start !== '16:00')
                                    <!-- Delete Button for Custom Tasks -->
                                    <form action="{{ route('todo.destroy', $task->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 5px 8px; color: var(--color-danger);" title="Hapus Tugas Custom"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="bi bi-calendar-x" style="font-size: 40px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                            Belum ada jadwal tugas hari ini. Silakan buat baru atau klik Reset Jadwal.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Date and Live Clock Setup
        const liveClock = document.getElementById('live-clock');
        const liveDateString = document.getElementById('live-date-string');

        function updateClockAndDate() {
            const now = new Date();
            
            // Format Live Clock
            let hrs = String(now.getHours()).padStart(2, '0');
            let mins = String(now.getMinutes()).padStart(2, '0');
            let secs = String(now.getSeconds()).padStart(2, '0');
            liveClock.textContent = `${hrs}:${mins}:${secs}`;
            
            // Format Live Date Indonsian
            const dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            
            liveDateString.textContent = `${dayNames[now.getDay()]}, ${now.getDate()} ${monthNames[now.getMonth()]} ${now.getFullYear()}`;
            
            // Update Active Task Indicators
            updateActiveTaskHighlight(hrs, mins);
        }

        function timeToMinutes(timeStr) {
            const [h, m] = timeStr.split(':').map(Number);
            return h * 60 + m;
        }

        function updateActiveTaskHighlight(hrs, mins) {
            const currentTotalMins = parseInt(hrs) * 60 + parseInt(mins);
            let activeFound = false;

            document.querySelectorAll('.task-card').forEach(card => {
                const startStr = card.getAttribute('data-start');
                const endStr = card.getAttribute('data-end');
                const activeIndicator = card.querySelector('.active-indicator');
                
                const startMins = timeToMinutes(startStr);
                const endMins = timeToMinutes(endStr);
                
                if (currentTotalMins >= startMins && currentTotalMins < endMins) {
                    activeFound = true;
                    card.style.background = 'rgba(99, 102, 241, 0.05)';
                    card.style.borderColor = 'rgba(99, 102, 241, 0.3)';
                    if (activeIndicator) activeIndicator.style.display = 'inline-block';
                    
                    // Sync top header details
                    document.getElementById('current-task-title').textContent = card.querySelector('h4').textContent.replace('●', '').trim();
                    document.getElementById('current-task-time').textContent = `${startStr} - ${endStr}`;
                } else {
                    card.style.background = 'rgba(255, 255, 255, 0.015)';
                    card.style.borderColor = 'var(--glass-border)';
                    if (activeIndicator) activeIndicator.style.display = 'none';
                }
            });

            if (!activeFound) {
                document.getElementById('current-task-title').textContent = 'Tidak Ada Aktivitas Jadwal Sekarang';
                document.getElementById('current-task-time').textContent = 'Waktu Luang / Istirahat';
            }
        }

        setInterval(updateClockAndDate, 1000);
        updateClockAndDate();

        // Toggle Custom Dropdowns
        document.querySelectorAll('.custom-status-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const taskId = this.getAttribute('data-id');
                const menu = document.getElementById(`status-menu-${taskId}`);
                
                // Close other dropdowns
                document.querySelectorAll('.custom-status-menu').forEach(otherMenu => {
                    if (otherMenu !== menu) {
                        otherMenu.style.display = 'none';
                    }
                });

                // Toggle this dropdown
                if (menu.style.display === 'block') {
                    menu.style.display = 'none';
                } else {
                    menu.style.display = 'block';
                }
            });
        });

        // Close dropdowns on outside click
        window.addEventListener('click', function () {
            document.querySelectorAll('.custom-status-menu').forEach(menu => {
                menu.style.display = 'none';
            });
        });

        // Custom Dropdown Option Handler
        document.querySelectorAll('.custom-status-option').forEach(option => {
            option.addEventListener('click', function (e) {
                e.stopPropagation();
                const menu = this.closest('.custom-status-menu');
                const taskId = menu.id.replace('status-menu-', '');
                const btn = document.querySelector(`.custom-status-btn[data-id="${taskId}"]`);
                const card = btn.closest('.task-card');
                const newStatus = this.getAttribute('data-value');

                // Determine border, bg and badge configurations
                let color = 'grey';
                let bgStyle = 'rgba(148, 163, 184, 0.12)';
                let borderStyle = 'rgba(148, 163, 184, 0.25)';
                let text = 'Belum Mulai';
                let emoji = '⚪';

                if (newStatus === 'sedang_dikerjakan') {
                    color = 'var(--color-primary)';
                    bgStyle = 'rgba(14, 165, 233, 0.12)';
                    borderStyle = 'rgba(14, 165, 233, 0.35)';
                    text = 'Dikerjakan';
                    emoji = '🔵';
                } else if (newStatus === 'menunggu') {
                    color = 'var(--color-warning)';
                    bgStyle = 'rgba(245, 158, 11, 0.12)';
                    borderStyle = 'rgba(245, 158, 11, 0.35)';
                    text = 'Menunggu';
                    emoji = '🟠';
                } else if (newStatus === 'selesai') {
                    color = 'var(--color-success)';
                    bgStyle = 'rgba(16, 185, 129, 0.12)';
                    borderStyle = 'rgba(16, 185, 129, 0.35)';
                    text = 'Selesai';
                    emoji = '🟢';
                } else if (newStatus === 'terlambat') {
                    color = 'var(--color-danger)';
                    bgStyle = 'rgba(239, 68, 68, 0.12)';
                    borderStyle = 'rgba(239, 68, 68, 0.35)';
                    text = 'Terlambat';
                    emoji = '🔴';
                }

                // Update UI instantly
                card.style.borderLeftColor = color;
                btn.setAttribute('data-current-status', newStatus);
                btn.style.background = bgStyle;
                btn.style.borderColor = borderStyle;
                btn.querySelector('.status-btn-content').innerHTML = `
                    <span>${emoji}</span>
                    <span>${text}</span>
                `;

                // Update active state in list
                menu.querySelectorAll('.custom-status-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                this.classList.add('active');

                // Hide menu
                menu.style.display = 'none';

                // Send AJAX Request
                fetch(`/todo-list/${taskId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        updateStatsAndProgressDOM();
                    }
                })
                .catch(err => console.error("Error updating status:", err));
            });
        });

        function updateStatsAndProgressDOM() {
            let total = 0;
            let selesai = 0;
            let sedang = 0;
            let menunggu = 0;
            let terlambat = 0;

            document.querySelectorAll('.custom-status-btn').forEach(btn => {
                total++;
                const val = btn.getAttribute('data-current-status');
                if (val === 'selesai') selesai++;
                else if (val === 'sedang_dikerjakan') sedang++;
                else if (val === 'menunggu') menunggu++;
                else if (val === 'terlambat') terlambat++;
            });

            // Update Counts text content
            document.getElementById('count-selesai').textContent = selesai;
            document.getElementById('count-sedang').textContent = sedang;
            document.getElementById('count-menunggu').textContent = menunggu;
            document.getElementById('count-terlambat').textContent = terlambat;

            // Update progress bar
            const pct = total > 0 ? Math.round((selesai / total) * 100) : 0;
            document.getElementById('progress-text').textContent = `${pct}% Selesai`;
            document.getElementById('progress-bar-fill').style.width = `${pct}%`;
        }

        // TIMER STOPWATCH FOCUS SYSTEM
        let timerInterval = null;
        let timeRemainingSeconds = 25 * 60; // Default 25 minutes
        const timerDisplay = document.getElementById('timer-display');
        const timerRing = document.getElementById('timer-ring');
        const btnStart = document.getElementById('btn-timer-start');
        const btnPause = document.getElementById('btn-timer-pause');
        const btnReset = document.getElementById('btn-timer-reset');

        function formatTimerDisplay(seconds) {
            let m = Math.floor(seconds / 60);
            let s = seconds % 60;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }

        function runTimerStep() {
            if (timeRemainingSeconds <= 0) {
                clearInterval(timerInterval);
                timerInterval = null;
                timeRemainingSeconds = 0;
                timerDisplay.textContent = "00:00";
                
                // Play notification sound
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = audioCtx.createOscillator();
                    osc.connect(audioCtx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(440, audioCtx.currentTime);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 1);
                } catch(e) {}

                alert("Waktu fokus tugas selesai! Ambil jeda istirahat sejenak.");
                btnStart.disabled = false;
                btnPause.disabled = true;
                return;
            }

            timeRemainingSeconds--;
            timerDisplay.textContent = formatTimerDisplay(timeRemainingSeconds);
            
            // Rotate circular ring
            const deg = (timeRemainingSeconds % 360) * 6;
            timerRing.style.transform = `rotate(${deg}deg)`;
        }

        btnStart.addEventListener('click', () => {
            if (timerInterval) return;
            timerInterval = setInterval(runTimerStep, 1000);
            btnStart.disabled = true;
            btnPause.disabled = false;
        });

        btnPause.addEventListener('click', () => {
            if (!timerInterval) return;
            clearInterval(timerInterval);
            timerInterval = null;
            btnStart.disabled = false;
            btnPause.disabled = true;
        });

        btnReset.addEventListener('click', () => {
            clearInterval(timerInterval);
            timerInterval = null;
            timeRemainingSeconds = 25 * 60;
            timerDisplay.textContent = "25:00";
            timerRing.style.transform = `rotate(0deg)`;
            btnStart.disabled = false;
            btnPause.disabled = true;
        });

        // Preset Timers Handlers
        document.querySelectorAll('.timer-preset-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                clearInterval(timerInterval);
                timerInterval = null;
                
                const mins = parseInt(this.getAttribute('data-minutes'));
                timeRemainingSeconds = mins * 60;
                timerDisplay.textContent = formatTimerDisplay(timeRemainingSeconds);
                timerRing.style.transform = `rotate(0deg)`;
                
                document.getElementById('timer-label').textContent = mins === 5 ? 'Rehat Sejenak' : 'Fokus Kerja';
                btnStart.disabled = false;
                btnPause.disabled = true;
            });
        });
    });
</script>
@endsection
