<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class ApiResponse implements Responsable
{
    protected int $httpCode;
    protected array $data;
    protected string $message;

    public function __construct(int $httpCode, array $data = [], string $message = '')
    {
        $this->httpCode = $httpCode;
        $this->data = $data;
        $this->message = $message;
    }
    
    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        $isSuccess = $this->httpCode >= 200 && $this->httpCode < 300;

        return response()->json([
            'status' => $isSuccess,
            'message' => $this->message,
            'data' => $isSuccess ? $this->data : null,
        ], $this->httpCode, options: JSON_UNESCAPED_UNICODE);
    }

    public static function success(array $data = [], string $message = '')
    {
        return new static(HttpCode::OK, $data, $message);
    }

    public static function ok(array $data = [], string $message = '')
    {
        return new static(HttpCode::OK, $data, $message);
    }

    public static function created(array $data = [], string $message = 'Resource created successfully')
    {
        return new static(HttpCode::CREATED, $data, $message);
    }

    public static function noContent(string $message = '')
    {
        return new static(HttpCode::NO_CONTENT, [], $message);
    }

    public static function badRequest(string $message = 'Bad request')
    {
        return new static(HttpCode::BAD_REQUEST, [], $message);
    }

    public static function unauthorized(string $message = 'Unauthorized')
    {
        return new static(HttpCode::UNAUTHORIZED, [], $message);
    }

    public static function forbidden(string $message = 'Forbidden')
    {
        return new static(HttpCode::FORBIDDEN, [], $message);
    }

    public static function notFound(string $message = 'Resource not found')
    {
        return new static(HttpCode::NOT_FOUND, [], $message);
    }

    public static function conflict(string $message = 'Conflict')
    {
        return new static(HttpCode::CONFLICT, [], $message);
    }

    public static function unprocessable(string $message = 'Unprocessable content')
    {
        return new static(HttpCode::UNPROCESSABLE_CONTENT, [], $message);
    }

    public static function error(string $message = 'Internal server error', int $code = HttpCode::INTERNAL_SERVER_ERROR)
    {
        return new static($code, [], $message);
    }
}