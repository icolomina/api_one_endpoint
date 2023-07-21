<?php

namespace Ict\ApiOneEndpoint\Contract\Operation;

use Ict\ApiOneEndpoint\Model\Api\ApiOutput;

interface OperationInterface
{
    public function perform(mixed $operationData): ApiOutput;
    public function getName(): string;
    public function getInput(): ?string;

    public function getGroup(): ?string;

    public function getContext(): ?array;
}
