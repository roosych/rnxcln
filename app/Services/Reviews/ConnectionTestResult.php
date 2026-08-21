<?php

namespace App\Services\Reviews;

final readonly class ConnectionTestResult
{
    private function __construct(
        public bool $ok,
        public string $message,
    ) {}

    public static function ok(string $message = 'Connected.'): self
    {
        return new self(true, $message);
    }

    public static function fail(string $message): self
    {
        return new self(false, $message);
    }
}
