<?php

namespace Contract\Api;

use Model\Api\ApiInput;
use Model\Api\ApiOutput;
use Symfony\Component\Security\Core\User\UserInterface;

interface OperationInterface
{
    public function perform(ApiInput $apiInput): ApiOutput;
    public function getName(): string;
    public function getInput(): string;

    public function getGroup(): ?string;
}
