<?php

namespace Ict\ApiOneEndpoint\Controller;

use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Ict\ApiOneEndpoint\Operation\OperationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

abstract class ApiOeController extends AbstractController
{
    protected function executeOperation(Request $request, SerializerInterface $serializer, OperationHandler $operationHandler): JsonResponse
    {
        $apiInput  = $serializer->deserialize($request->getContent(), ApiInput::class, 'json');
        $apiOutput = $operationHandler->performOperation($apiInput);

        return new JsonResponse(
            $serializer->normalize($apiOutput->getData()),
            $apiOutput->getCode()
        );
    }
}
