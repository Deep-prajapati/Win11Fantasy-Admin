<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypesValue extends Model
{
    protected $fillable = [
        'type_id',
        'name',
        'code',
        'model_type',
        'developer_name',
        'stat_group',
    ];

    protected $attributes = [
        'name' => '',
        'code' => '',
        'model_type' => '',
        'developer_name' => '',
        'stat_group' => '',
    ];
}
