<?php

/**
 * A JSON response. Success bodies are `{ "data": ... }` with an optional
 * `{ "meta": ... }`; error bodies are `{ "error": { "message", "code", ... } }`.
 */
class ApiResponse
{
    public function __construct(
        public mixed $data,
        public int $status = 200,
        public ?array $meta = null,
    ) {
    }

    public static function ok(mixed $data, ?array $meta = null): self
    {
        return new self($data, 200, $meta);
    }

    public static function collection(array $items, array $meta = []): self
    {
        return new self(array_values($items), 200, $meta ?: null);
    }

    public static function created(mixed $data): self
    {
        return new self($data, 201);
    }

    public static function noContent(): self
    {
        return new self(null, 204);
    }

    public static function send(mixed $result): void
    {
        if (!$result instanceof self) {
            $result = new self($result);
        }

        http_response_code($result->status);
        self::baseHeaders();

        if ($result->status === 204) {
            return;
        }

        $body = ['data' => $result->data];
        if ($result->meta !== null) {
            $body['meta'] = $result->meta;
        }

        echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function sendError(ApiException $e): void
    {
        http_response_code($e->getStatus());
        self::baseHeaders();

        $error = ['message' => $e->getMessage()];
        if ($e->getErrorCode() !== '') {
            $error['code'] = $e->getErrorCode();
        }
        foreach ($e->getExtra() as $key => $value) {
            $error[$key] = $value;
        }

        echo json_encode(['error' => $error], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function baseHeaders(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store');
    }
}
