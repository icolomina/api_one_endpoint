<?php

namespace Ict\ApiOneEndpoint\Contract\Operation;

use Model\Api\ApiInput;
use Model\Api\ApiOutput;

interface OperationInterface
{
    public function perform(ApiInput $apiInput): ApiOutput;
    public function getName(): string;
    public function getInput(): string;

    public function getGroup(): ?string;
}
