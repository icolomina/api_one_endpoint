<?php

namespace Ict\ApiOneEndpoint\Model\Api;

class ApiOutput
{
    private iterable|object $data;
    private int $code;

    private ?string $serializerGroup = null;

    public function __construct(iterable|object $data, int $code, ?string $serializerGroup = null)
    {
        $this->data = $data;
        $this->code = $code;
        $this->serializerGroup = $serializerGroup;
    }

    public function getData(): iterable|object
    {
        return $this->data;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getSerializerGroup(): ?string
    {
        return $this->serializerGroup;
    }


}
