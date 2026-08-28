<?php

/**
 * Any error that should be rendered as a JSON error response.
 * The HTTP status is carried in the standard Exception `code`.
 */
class ApiException extends Exception
{
    private string $errorCode;
    private array $extra;

    public function __construct(int $status, string $message, string $errorCode = '', array $extra = [])
    {
        parent::__construct($message, $status);
        $this->errorCode = $errorCode;
        $this->extra = $extra;
    }

    public function getStatus(): int
    {
        return (int) $this->getCode();
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public static function notFound(string $message = 'Resource not found'): self
    {
        return new self(404, $message, 'not_found');
    }

    public static function methodNotAllowed(string $message = 'Method not allowed'): self
    {
        return new self(405, $message, 'method_not_allowed');
    }

    public static function validation(array $fields, string $message = 'Validation failed'): self
    {
        return new self(422, $message, 'validation_error', ['fields' => $fields]);
    }

    public static function unauthorized(string $message = 'Authentication required'): self
    {
        return new self(401, $message, 'unauthorized');
    }
}
