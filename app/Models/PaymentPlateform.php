<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentPlateform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'enabled',

    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
