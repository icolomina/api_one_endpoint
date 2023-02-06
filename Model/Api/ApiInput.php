<?php

namespace Ict\ApiOneEndpoint\Model\Api;

class ApiInput
{
    /**
     * Operation we have to perform
     */
    private string $operation;

    /**
     * Data which have to be passed to the operation
     */
    private array $data;

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): void
    {
        $this->operation = $operation;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
