<?php

namespace Ict\ApiOneEndpoint\Model\Attribute;


#[\Attribute]
class IsBackground
{
    public function __construct(
        public readonly ?int $delay = null
    ){ }
}
