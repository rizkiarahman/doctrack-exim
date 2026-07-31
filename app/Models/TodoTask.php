<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'time_start',
        'time_end',
        'status',
        'task_date',
        'notes',
    ];

    /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
