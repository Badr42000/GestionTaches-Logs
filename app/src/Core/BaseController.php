<?php

namespace App\Core;

abstract class BaseController
{
    /** @param array<string, mixed> $data */
    protected function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../templates/layout.php';
    }
}
