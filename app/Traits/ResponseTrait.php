<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ResponseTrait
{
    /**
     * @param $message
     * @param $data
     * @return array
     */
    public static function ok($message = '', $data = []): array
    {
        return [
            'success' => true,
            'code'    => Response::HTTP_OK,
            'message' => self::getMessage($message),
            'data'    => $data
        ];
    }

    /**
     * @param $message
     * @param $data
     * @param $pagination
     * @return array
     */
    public static function okPaginated($pagination, $message = '', $data = []): array
    {
        return [
            'success' => true,
            'code'    => Response::HTTP_OK,
            'message' => self::getMessage($message),
            'pagination' => $pagination,
            'data'    => $data,
        ];
    }
    /**
     * Return a bad request response, with code 400
     *
     * @param $message
     * @return array
     */
    public static function badRequest($message): array
    {
        return [
            'success' => false,
            'code'    => Response::HTTP_BAD_REQUEST,
            'message' => self::getMessage($message)
        ];
    }

    /**
     * Return an invalid response, with code 422
     *
     * @param $message
     * @param array $errors
     * @return array
     */
    public static function invalidRequest($message, array $errors = []): array
    {
        return [
            'success' => false,
            'code'    => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => self::getMessage($message),
            'errors'  => $errors
        ];
    }


    /**
     * Return a not found response, with code 404
     *
     * @param $message
     * @return array
     */
    public static function notFound($message): array
    {
        return [
            'success' => false,
            'code'    => Response::HTTP_NOT_FOUND,
            'message' => self::getMessage($message),
            'data'    => []
        ];
    }

    /**
     * Return an unauthorised response, with code 401
     *
     * @param $message
     * @return array
     */
    public static function unauthorised($message): array
    {
        return [
            'success' => false,
            'code'    => Response::HTTP_UNAUTHORIZED,
            'message' => self::getMessage($message)
        ];
    }

    /**
     * Return an error response, with code 500
     *
     * @param $message
     * @param array $errors
     * @return array
     */
    public static function error($message, array $errors = [], $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR): array
    {
        return [
            'success' => false,
            'code'    => $httpCode,
            'message' => self::getMessage($message),
            'errors'  => $errors
        ];
    }

    /**
     * Return an exception response, with code 500
     *
     * @param $exception
     * @return array
     */
    public static function exception($exception): array
    {
        return [
            'success' => false,
            'code'    => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $exception->getMessage(),
            'errors'  => [
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine()
            ]
        ];
    }

    /**
     * Outputs dump to API too after logging for debugging
     *
     * @param $exception
     * @return void
     */
    public static function logError($exception)
    {
        Log::error($exception->getMessage(), $exception->getTrace());

        if (in_array(config('app.env'), ['local', 'dev'])) {
            dd($exception); // this is real code; not debug
        }
    }

    /**
     * Return a raw response, with code 200 by default but this can be defines
     *
     * @param $payload
     * @param $status
     * @return JsonResponse
     */
    public static function raw($payload = [], $status = 200): JsonResponse
    {
        return response()->json($payload, $status);
    }

    /**
     * get pre-defined messages from the language files
     *
     * @param String $message
     * @return array|String
     */
    public static function getMessage(string $message): array|string
    {
        if (empty($message)) {
            return __('general.missing_lang');
        }

        return $message;
    }

    public function responseWrapperGeneric($response)
    {
        if ($response) {
            return response()->json($this->ok(__('general.index.success'), $response));
        } else {
            throw new HttpResponseException(response()->json($this->unauthorised(__('auth.unauthorized')), 401));
        }
    }
}
