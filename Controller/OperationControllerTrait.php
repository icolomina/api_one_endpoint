<?php

namespace Ict\ApiOneEndpoint\Controller;

use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Ict\ApiOneEndpoint\Model\Api\Context;
use Ict\ApiOneEndpoint\Operation\OperationHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

trait OperationControllerTrait
{
    public function executeOperation(Request $request, SerializerInterface $serializer, OperationHandler $operationHandler, Context $context): JsonResponse
    {
        $apiInput  = $serializer->deserialize($request->getContent(), ApiInput::class, 'json');
        $apiOutput = $operationHandler->performOperation($apiInput, $context);
        $context   = [];

        if($apiOutput->getSerializerGroup()){
            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups($apiOutput->getSerializerGroup())
                ->toArray()
            ;
        }

        return new JsonResponse(
            $serializer->normalize($apiOutput->getData(), null, $context),
            $apiOutput->getCode()
        );
    }
}
