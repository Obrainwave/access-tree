<?php

namespace Obrainwave\AccessTree\Responses;

class AccessTreeResponse
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data = null,
        public int $statusCode = 200,
        public array $errors = []
    ) {}

    public static function success(string $message, mixed $data = null, int $statusCode = 200): self
    {
        return new self(true, $message, $data, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, array $errors = []): self
    {
        return new self(false, $message, null, $statusCode, $errors);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isError(): bool
    {
        return !$this->success;
    }
}
