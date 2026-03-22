<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{

    protected $fillable = ['name', 'value'];
    
    public static function getValue($key, $default = null)
    {
        return optional(static::where('name', $key)->first())->value ?? $default;
    }
    public static function setValue($key, $value)
    {
        return static::updateOrCreate(
            ['name' => $key],
            ['value' => $value]
        );
    }
}
