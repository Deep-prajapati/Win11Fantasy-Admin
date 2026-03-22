<?php

namespace App\Helpers;

use App\Exceptions\PublicException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Storage;

class Helper
{

    public static function SuccessReturn($data = [], string $messageKey = '', array $replace = [], array $receiverIds = [],)
    {

        $message = $messageKey ? __("message." . $messageKey, $replace) : '';
        if (__("message." . $messageKey)  == "message." . $messageKey) {
            $message = $messageKey;
        }
        $reponse = ['success' => true, 'status' => config('constant.STATUS_OK'), 'message' => $message, 'data' => $data];
        if ($receiverIds) {
            $reponse['receiver_ids'] = $receiverIds;
        }
        return response()->json($reponse, config('constant.STATUS_OK'));
    }
    public static function StatusReturn($data = [], string $messageKey = '', array $replace = [], array $receiverIds = [], $statusCode = null)
    {
        $statusCode = $statusCode ?? config('constant.STATUS_OK');

        $message = $messageKey ? __("message." . $messageKey, $replace) : '';
        if (__("message." . $messageKey) == "message." . $messageKey) {
            $message = $messageKey;
        }
        $reponse = ['success' => true, 'status' => $statusCode, 'message' => $message, 'data' => $data];
        if ($receiverIds) {
            $reponse['receiver_ids'] = $receiverIds;
        }
        return response()->json($reponse, $statusCode);
    }


    public static function FalseReturn($data = [], string $messageKey = '')
    {

        $message = $messageKey ? __("message." . $messageKey) : '';
        if (__("message." . $messageKey)  == "message." . $messageKey) {
            $message = $messageKey;
        }
        return response()->json(['success' => false, 'status' => config('constant.STATUS_OK'), 'message' => $message, 'data' => $data], config('constant.STATUS_OK'));
    }


    public static function UpdateObjectIfKeyExist(object $object, object $request, array $keys)
    {
        foreach ($keys as $key) {

            if ($request->has($key)) {
                $object->$key = $request->$key;
            }
        }
        return $object;
    }

    public static function UpdateObjectIfKeyNotEmpty(object $object, object $request, array $keys)
    {
        foreach ($keys as $key) {
            if ($request->has($key) && !empty($request->$key)) {
                $object->$key = $request->$key;
            }
        }
        return $object;
    }

    public static function EmptyReturn(string $messageKey = '', array $replace = [])
    {
        $message = $messageKey ? __("message." . $messageKey) : '';
        if (__("message." . $messageKey)  == "message." . $messageKey) {
            $message = $messageKey;
        }
        return response()->json(['success' => false, 'status' => config('constant.STATUS_OK'), 'message' => $message, 'data' => null], config('constant.STATUS_OK'));
    }

    public static function SuccessReturnPagination($data = [], $totalPage = 0, $nextPage = "", string $messageKey = '', array $replace = [])
    {

        $message = $messageKey ? __("message." . $messageKey) : '';
        if (__("message." . $messageKey)  == "message." . $messageKey) {
            $message = $messageKey;
        }
        return response()->json(['success' => true, 'status' => config('constant.STATUS_OK'), 'message' => $message, 'data' => $data, 'total_page' => $totalPage, 'next_page' => $nextPage], config('constant.STATUS_OK'));
    }
}
