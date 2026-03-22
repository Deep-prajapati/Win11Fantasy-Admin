<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method',
        'amount',
        'status',
        'details',
    ];

    protected $casts = [
        'details' => 'array',

    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
