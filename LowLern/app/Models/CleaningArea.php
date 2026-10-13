<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningArea extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function cleaningLogs()
    {
        return $this->hasMany(CleaningLog::class);
    }
}