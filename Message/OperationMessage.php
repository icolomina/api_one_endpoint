<?php

namespace Ict\ApiOneEndpoint\Message;

use Ict\ApiOneEndpoint\Model\Api\ApiInput;

class OperationMessage
{
    public function __construct(
        public readonly ApiInput $apiInput,
        public readonly ?string $userIdentifier = null
    ){ }
}
