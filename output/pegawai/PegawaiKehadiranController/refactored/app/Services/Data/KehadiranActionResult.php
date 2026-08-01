<?php

namespace App\Services\Data;

final readonly class KehadiranActionResult
{
    public function __construct(
        public string $flashKey,
        public string $message
    ) {
    }
}