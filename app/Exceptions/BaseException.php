<?php

declare(strict_types=1);

namespace Sputnik\Exceptions;

use Exception;

class BaseException extends Exception
{
    const FLIGHT_PROGRAM = 10;
    const REQUEST = 11;
    const INVALID_EXCHANGE_RESPONSE = 12;
    const OPERATION = 13;
    const EVENT = 14;

    protected array $context = [];

    public function getContext(): array
    {
        return $this->context;
    }

    protected function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }
}
