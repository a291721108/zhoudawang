<?php

namespace App\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;


trait Api
{

    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode(): mixed
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode): static
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return mixed
     */
    public function respond($data, $header = []): mixed
    {
        return Response::json($data, $this->getStatusCode(), $header);
    }

    /**
     * @param $status
     * @param array $data
     * @param null $code
     * @return mixed
     */
    public function status($status, array $data, $code = null): mixed
    {
        if ($code) {
            $this->setStatusCode($code);
        }
        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];
        $data = array_merge($status, $data);
        return $this->respond($data);
    }

    /**
     * @param $message
     * @param int $code
     * @param string $status
     * @return mixed
     */
    public function failed($message, int $code = FoundationResponse::HTTP_BAD_REQUEST, string $status = 'error'): mixed
    {
        return $this->setStatusCode($code)->message($message, $status);
    }

    /**
     * @param $message
     * @param string $status
     * @return mixed
     */
    public function message($message, string $status = "error"): mixed
    {
        return $this->status($status, [
            'message' => $message
        ]);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function internalError(string $message = "Internal Server Error!"): mixed
    {
        return $this->failed($message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function created(string $message = "created")
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
            ->message($message);
    }

    /**
     * 请求方法不存在
     * @param string $message
     * @return JsonResponse
     */
    public function methodNotAllow(string $message = 'Method Not Allowed!'): JsonResponse
    {
        return $this->setStatusCode(FoundationResponse::HTTP_METHOD_NOT_ALLOWED)
            ->message($message);
    }

    /**
     * 身份验证失败响应。
     * @param string $message
     */
    public function unAuthorized(string $message = 'Unauthorized.'): JsonResponse
    {
        return $this->setStatusCode(FoundationResponse::HTTP_UNAUTHORIZED)
            ->message($message);
    }

    /**
     * 服务器位置错误响应。
     * @param string $message
     */
    public function serviceUnavailable(string $message = 'Service Unavailable!'): JsonResponse
    {
        return $this->setStatusCode(FoundationResponse::HTTP_SERVICE_UNAVAILABLE)
            ->message($message);
    }

    /**
     * 权限不足响应。
     * @param string $message
     */
    public function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return $this->setStatusCode(FoundationResponse::HTTP_FORBIDDEN)
            ->message($message);
    }

    /**
     * 表单验证错误响应。
     * @param string $message
     */
    public function badRequest($message = 'Bad Request!')
    {
        return $this->setStatusCode(FoundationResponse::HTTP_BAD_REQUEST)
            ->message($message);
    }

    /**
     * @param $data
     * @param string $status
     * @return mixed
     */
    public function success($data, $status = "success")
    {
        return $this->status($status, compact('data'));
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message, Foundationresponse::HTTP_NOT_FOUND);
    }

}
