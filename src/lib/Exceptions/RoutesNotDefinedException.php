<?php

declare(strict_types=1);

namespace FFPerera\Cubo\Exceptions;

class RoutesNotDefinedException extends \InvalidArgumentException
{
    public function __construct(string $message = 'No Routes Found', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
