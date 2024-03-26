<?php

namespace App\Services;

use App\Domains\Panel\Services\SmartList\SmartListResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class ResponseService
{
    public const DEFAULT_JSON_HEADER = [
        'Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8',
    ];

    public function __construct(
        private readonly mixed $data,
        private readonly int $statusCode = 200,
        private readonly array $headers = [],
        private readonly bool $wrap = true,
    ) {
        //
    }

    public static function make(...$parameters): self
    {
        return new self(...$parameters);
    }

    public function json(): JsonResponse
    {
        $headers = [
            ...self::DEFAULT_JSON_HEADER,
            ...$this->headers,
        ];

        $options = app()->isLocal() ? JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT : JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;

        return new JsonResponse(
            $this->formatData(),
            $this->statusCode,
            $headers,
            $options,
        );
    }

    public function formatData(): mixed
    {
        if (! $this->wrap) {
            return $this->data;
        }

        $data = $this->getData();
        $pagination = $this->getPagination();
        $additional = $this->getAdditionalData();

        $result = [
            'data' => $data,
        ];

        if ($additional) {
            $result = [
                ...$result,
                ...$additional,
            ];
        }

        if ($pagination) {
            $result['meta']['paginate'] = $pagination;
        }

        return array_filter_null(
            $result
        );
    }

    public function getData(): mixed
    {
        if ($this->data instanceof Collection) {
            return $this->data;
        }

        $resource = $this->data->resource ?? null;

        if ($resource instanceof AbstractPaginator) {
            return $resource->getCollection();
        }

        if (isset($this->data->collects) && is_subclass_of($this->data->collects, SmartListResource::class)) {
            return $resource;
        }

        return $this->data;
    }

    private function getAdditionalData(): ?array
    {
        if (! isset($this->data->additional) || ! is_array($this->data->additional)) {
            return null;
        }

        return $this->data->additional;
    }

    private function getPagination(): ?array
    {
        if ($this->data instanceof Collection) {
            return null;
        }

        $resource = $this->data->resource ?? null;

        if ($resource instanceof LengthAwarePaginator) {
            return [
                'total' => $resource->total(),
                'count' => $resource->count(),
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'total_pages' => $resource->lastPage(),
            ];
        }

        if ($resource instanceof CursorPaginator) {
            return [
                'per_page' => $resource->perPage(),
                'has_more' => $resource->hasMorePages(),
                'cursor_name' => $resource->getCursorName(),
                'next_cursor' => $resource->nextCursor()?->encode(),
            ];
        }

        if ($resource instanceof Paginator) {
            return [
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'has_more' => $resource->hasMorePages(),
            ];
        }

        return null;
    }
}
