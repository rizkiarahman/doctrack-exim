<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'user_id',
    ];

    /**
     * Get the user who uploaded the file.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     * Get file extension and category properties (icon & color).
     */
    public function getFileCategoryAttribute(): array
    {
        $ext = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['pdf'])) {
            return ['icon' => 'bi-file-earmark-pdf-fill', 'color' => '#f43f5e', 'bg' => 'rgba(244, 63, 94, 0.12)', 'label' => 'PDF Document'];
        } elseif (in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return ['icon' => 'bi-file-earmark-excel-fill', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.12)', 'label' => 'Excel Spreadsheet'];
        } elseif (in_array($ext, ['docx', 'doc'])) {
            return ['icon' => 'bi-file-earmark-word-fill', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.12)', 'label' => 'Word Document'];
        } elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif'])) {
            return ['icon' => 'bi-file-earmark-image-fill', 'color' => '#a855f7', 'bg' => 'rgba(168, 85, 247, 0.12)', 'label' => 'Gambar / Foto'];
        } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return ['icon' => 'bi-file-earmark-zip-fill', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.12)', 'label' => 'Arsip Terkompresi'];
        } elseif (in_array($ext, ['txt', 'json', 'xml', 'html', 'php', 'js', 'css'])) {
            return ['icon' => 'bi-file-earmark-code-fill', 'color' => '#06b6d4', 'bg' => 'rgba(6, 182, 212, 0.12)', 'label' => 'Teks / Kode'];
        } else {
            return ['icon' => 'bi-file-earmark-fill', 'color' => '#64748b', 'bg' => 'rgba(100, 116, 139, 0.12)', 'label' => 'Berkas Lainnya'];
        }
    }
}
