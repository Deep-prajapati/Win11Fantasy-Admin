<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        $largeException = parent::render($request, $exception);
        $statusCode = $exception->getCode();
        if ($exception instanceof PublicException) {
            return $exception->render($exception->getMessage(), $statusCode);
        }
        if ($request->route() && ($request->route()->getPrefix() == "api" || Str::before($request->route()->getPrefix(), '/') == 'api')) {

            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['success' => FALSE, 'status' => $statusCode, 'message' => __("message.NOT_FOUND")], STATUS_OK);
            }
            if ($exception instanceof AuthenticationException) {
                return response()->json(['success' => FALSE, 'status' => STATUS_UNAUTHORIZED, 'message' => __("message.UNAUTHORIZED_ACCESS")], STATUS_UNAUTHORIZED);
            }
            $response = [];
            $response['message'] = 'Error occurred, please contact App Support. Error Code = ' . $statusCode;
            $response['success'] = FALSE;
            $response['status'] = $statusCode;
            if (config('app.debug')) {
                $response['trace'] = $exception->getMessage();
                $response['line'] = $exception->getLine();
                $response['file'] = $exception->getFile();
                $response['code'] = $exception->getCode();
            }
            return response()->json($response, STATUS_BAD_REQUEST);

        }else {
            if ($exception instanceof ModelNotFoundException) {
                return redirect()->back()->with('error', __("message.NOT_FOUND"));
            }
            return parent::render($request, $exception);
        }
    }
}
