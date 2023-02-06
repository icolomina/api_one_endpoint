<?php

namespace Ict\ApiOneEndpoint\Operation;

use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;

class OperationCollection
{
    /**
     * @var array<string, OperationInterface>
     */
    private array $operations;

    public function __construct(iterable $apiOperations)
    {
        foreach ($apiOperations as $operation){
            $this->operations[$operation->getName()] = $operation;
        }
    }

    public function getOperation(string $operation): ?OperationInterface
    {
        return $this->operations[$operation] ?? null;
    }

    public function hasOperation(string $operation): bool
    {
        return isset($this->operations[$operation]);
    }
}
