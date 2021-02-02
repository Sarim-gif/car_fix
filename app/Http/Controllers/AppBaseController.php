<?php

namespace App\Http\Controllers;

use InfyOm\Generator\Utils\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/car_fix/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    public function sendErrorWithData($error, $code = 404, $data = [])
    {
        if (empty($data)) {
            if (is_array($error)) {
                $data = ['errors' => $error];
            } else {
                $data = ['errors' => ["error" => [$error]]];
            }
        }
        return Response::json(ResponseUtil::makeError('Not Found', $data), $code);
    }
}