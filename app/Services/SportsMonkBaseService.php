<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class SportsMonkBaseService
{

    protected $apiToken;
    protected $baseUrl;

    public function __construct()
    {
        // $this->apiKey = 'ZFGjcHonzs3SBvCCoA8DryxO3Y0q0BafBg6b1tLBfLVjaVKhE0bvIqhtEMC6';//paid key
        $this->apiToken =  SiteSettings::getValue('sportsmonk_api_key', 'i4Ajujc8Ir2PaUHTvC0OV0a9s61fAKzUHl1MJDSaeljXtRayBNS13APFbydy');
        $this->baseUrl = 'https://api.sportmonks.com/v3/core/';
    }
    public function fetchAllTypes(): array
    {
        $allTypes = [];
        $nextUrl = $this->baseUrl . 'types?api_token=' . $this->apiToken;
        while ($nextUrl) {
            $response = Http::get($nextUrl);

            if ($response->failed()) {
                Log::error('Sportmonks API failed', ['url' => $nextUrl]);
                break;
            }

            $data = $response->json();

            if (!empty($data['data'])) {
                $allTypes = array_merge($allTypes, $data['data']);
            }

            $nextUrl = $data['pagination']['next_page'] ?? null;
            if ($nextUrl) {
                $parsedUrl = parse_url($nextUrl);
                $separator = isset($parsedUrl['query']) ? '&' : '?';
                $nextUrl .= $separator . 'api_token=' . $this->apiToken;
            }
        }
        return $allTypes;
    }
}
