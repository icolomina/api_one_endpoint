<?php

namespace Ict\ApiOneEndpoint\Operation;

use Ict\ApiOneEndpoint\Exception\OperationValidationException;
use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OperationInputValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer
    ){ }

    public function validateInput(ApiInput $apiInput, string $inputModel): object
    {
        $denormalizedObject = $this->serializer->denormalize($apiInput->getData(), $inputModel);
        $validationErrors   = $this->validator->validate($denormalizedObject);
        if(count($validationErrors) > 0){
            throw new OperationValidationException($validationErrors);
        }

        return $denormalizedObject;
    }
}
