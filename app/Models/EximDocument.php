<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EximDocument extends Model
{
    protected $fillable = [
        'no_aju',
        'pic',
        'tgl_diserahkan',
        'tgl_kembali',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tgl_diserahkan' => 'date',
        'tgl_kembali' => 'date'
    ];

    /**
     * Boot the model.
     * Automatically transitions status to 'Perlu Follow Up' if the document
     * has been pending for >= 7 days when retrieved.
     */
    protected static function booted()
    {
        static::retrieved(function ($document) {
            if ($document->status !== 'Sudah Kembali' && !$document->tgl_kembali) {
                $days = Carbon::parse($document->tgl_diserahkan)->startOfDay()->diffInDays(now()->startOfDay());
                if ($days >= 7 && $document->status !== 'Perlu Follow Up') {
                    $document->status = 'Perlu Follow Up';
                    $document->save();
                }
            }
        });

        static::saving(function ($document) {
            // Also enforce status update on saving if tgl_kembali is not set
            if ($document->status !== 'Sudah Kembali' && !$document->tgl_kembali) {
                $days = Carbon::parse($document->tgl_diserahkan)->startOfDay()->diffInDays(now()->startOfDay());
                if ($days >= 7) {
                    $document->status = 'Perlu Follow Up';
                } else {
                    $document->status = 'Menunggu Tanda Tangan';
                }
            } elseif ($document->tgl_kembali) {
                $document->status = 'Sudah Kembali';
            }
        });
    }

    /**
     * Accessor for days pending.
     */
    public function getDaysPendingAttribute()
    {
        $start = Carbon::parse($this->tgl_diserahkan)->startOfDay();
        $end = $this->tgl_kembali ? Carbon::parse($this->tgl_kembali)->startOfDay() : now()->startOfDay();
        return $start->diffInDays($end);
    }
}
