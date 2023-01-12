<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

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
    public static function invalid($message, array $errors = []): array
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
            'message' => self::getMessage($message)
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
    public static function error($message, array $errors = []): array
    {
        return [
            'success' => false,
            'code'    => Response::HTTP_INTERNAL_SERVER_ERROR,
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
    public static function getMessage(String $message): array|String
    {
        if (empty($message)) {
            return __('general.missing_lang');
        }

        return $message;
    }
}
