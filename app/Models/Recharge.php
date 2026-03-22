<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'user_id',
        'amount',
        'order_id',
        'tnx_id',
        'status',
        'image',
        'utr_no',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recharge) {
            $recharge->method = $recharge->method ?? 1; // Default to 1 if not set
            $recharge->tnx_id = $recharge->tnx_id ?? 'tnx_' . Str::random(10);
            $recharge->order_id = $recharge->order_id ?? 'ord_' . Str::random(10);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
