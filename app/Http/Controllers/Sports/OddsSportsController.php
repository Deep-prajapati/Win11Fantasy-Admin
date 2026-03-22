<?php

namespace App\Http\Controllers\Sports;

use App\Models\TypesValue;
use App\Http\Controllers\Controller;
use App\Services\SportsMonkBaseService;

class OddsSportsController extends Controller
{

    protected $footballApi;

    public function __construct()
    {
        $this->footballApi = new SportsMonkBaseService();
    }
    public function getBaseTypes(){
        $service = new SportsMonkBaseService();
        $response =  $service->fetchAllTypes();
        $records = collect($response)->map(function ($data) {
            return [
                'type_id' => $data['id'],
                'name' => $data['name'] ?? '',
                'code' => $data['code'] ?? '',
                'model_type' => $data['model_type'] ?? '',
                'developer_name' => $data['developer_name'] ?? '',
                'stat_group' => $data['stat_group'] ?? '',
            ];
        })->all();

        TypesValue::upsert($records, ['type_id'], ['name', 'code', 'model_type', 'developer_name', 'stat_group']);
    }
}
