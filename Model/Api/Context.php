<?php

namespace Ict\ApiOneEndpoint\Model\Api;

class Context
{
    public function __construct(
        public readonly ?string $context = null
    ){}

    public function isEmpty(): bool
    {
        return empty($this->context);
    }
}
