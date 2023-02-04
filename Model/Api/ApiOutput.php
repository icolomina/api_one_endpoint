<?php

namespace Model\Api;

class ApiOutput
{
    private iterable|object $data;
    private int $code;

    public function __construct(iterable|object $data, int $code)
    {
        $this->data = $data;
        $this->code = $code;
    }

    public function getData(): iterable|object
    {
        return $this->data;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
