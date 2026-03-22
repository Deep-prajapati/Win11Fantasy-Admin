<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContestType extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_type',
        'description',
        'max_entries',
        'free_wheel_count',
        'cancellable',
        'is_deleted',
    ];
    protected $hidden = [
        'cancellable',
        'is_deleted',
    ];
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }
}
