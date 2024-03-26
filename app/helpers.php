<?php

use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

function apiNoWrap($data, int $status = 200, array $headers = []): JsonResponse
{
    return api($data, $status, $headers, false);
}

function api($data, int $statusCode = 200, array $headers = [], $wrap = true): JsonResponse
{
    return ResponseService::make($data, $statusCode, $headers, $wrap)
        ->json();
}

function apiSuccess(bool $bool = true): JsonResponse
{
    return api([
        'success' => $bool
    ]);
}

function array_filter_null(array $array): array
{
    return array_filter($array, function ($value) {
        return $value !== null;
    });
}
