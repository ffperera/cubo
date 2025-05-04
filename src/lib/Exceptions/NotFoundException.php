<?php

declare(strict_types=1);

namespace FFPerera\Cubo\Exceptions;

class NotFoundException extends \InvalidArgumentException
{
    public function __construct(string $message = 'Not Found', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
