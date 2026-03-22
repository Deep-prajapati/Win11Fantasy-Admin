<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'bonus',
        'winning',
        'bank_holder_name',
        'bank_name',
        'bank_ifsc',
        'bank_account',
        'upi_id',
        'upi_name',
    ];

    /**
     * Get the user associated with the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
