<?php

namespace Ict\ApiOneEndpoint\Message;

use Model\Api\ApiInput;

class OperationMessage
{
    public function __construct(
        public readonly ApiInput $apiInput,
        public readonly ?string $userIdentifier = null
    ){ }
}
