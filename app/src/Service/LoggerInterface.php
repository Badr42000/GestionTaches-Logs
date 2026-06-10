<?php

namespace App\Service;

interface LoggerInterface
{
    public function send(string $severity, string $tag, string $message): void;
}
