<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'cleaning_area_id',
        'assigned_by',
        'title',
        'status',
        'before_image',
        'after_image',
        'scheduled_date',
        'completed_at',
    ];

    public function cleaningArea()
    {
        return $this->belongsTo(CleaningArea::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}