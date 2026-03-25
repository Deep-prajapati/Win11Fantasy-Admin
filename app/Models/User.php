<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'image',
        'country_code',
        'otp_token',
        'role',
        'password',
        'ref_code',
        'invite_code',
        "fcm_token"
    ];
    protected $hidden = [
        'otp_token',
        'remember_token',
        'role',
        'password'
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function account()
    {
        return $this->hasOne(UserWallet::class, 'user_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $lastUser = static::latest('invite_code')->first();

            if ($lastUser) 
            {
                $lastNumber = (int)substr($lastUser->invite_code, strlen('Win11Circle'));
                $userCode = $lastNumber + 1;
            } 
            else 
            {
                $userCode = 2345;
            }
            $user->invite_code = 'Win11Circle' . str_pad($userCode, 5, '0', STR_PAD_LEFT);
        });
    }

    private function generateRefCode()
    {
        $prefix = 'Win11Circle';
        $lastUser = User::whereNotNull('invite_code')->orderBy('id', 'desc')->first();

        $startValue = 2345;
        $nextNumber = $lastUser ? ((int) substr($lastUser->invite_code, strlen($prefix)) + 1) : $startValue;

        return $prefix . $nextNumber;
    }

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
}
