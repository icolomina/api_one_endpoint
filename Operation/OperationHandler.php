<?php

namespace Ict\ApiOneEndpoint\Operation;

use Ict\ApiOneEndpoint\Attribute\AttributeHelper;
use Ict\ApiOneEndpoint\Exception\OperationNotDefinedException;
use Ict\ApiOneEndpoint\Message\OperationMessage;
use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Ict\ApiOneEndpoint\Model\Attribute\IsBackground;
use Ict\ApiOneEndpoint\Model\Operation\OperationSubject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OperationHandler
{
    public function __construct(
        private readonly OperationCollection $operationCollection,
        private readonly MessageBusInterface $bus,
        private readonly AttributeHelper $attributeHelper,
        private readonly OperationInputValidator $operationInputValidator,
        private readonly mixed $security
    ){ }

    public function performOperation(ApiInput $apiInput): ApiOutput
    {
        if(!$this->operationCollection->hasOperation($apiInput->getOperation())){
            throw new OperationNotDefinedException($apiInput->getOperation());
        }

        $operationHandler = $this->operationCollection->getOperation($apiInput->getOperation());
        if(!empty($operationHandler->getInput())){
            $this->operationInputValidator->validateInput($apiInput, $operationHandler->getInput());
        }

        $isGranted = $this->security->isGranted('EXECUTE_OPERATION', new OperationSubject(get_class($operationHandler), $operationHandler->getGroup()));
        if(!$isGranted) {
            throw new AccessDeniedException('Not allowed to perform this operation');
        }

        $attrObject = $this->attributeHelper->getAttr($operationHandler, IsBackground::class);
        if($attrObject){
            $stamps = ($attrObject->delay > 0) ? [new DelayStamp($attrObject->delay * 1000)] : [];
            $userIdentifier  = $this->security->getToken()?->getUser()?->getUserIdentifier();

            $this->bus->dispatch(new OperationMessage($apiInput, $userIdentifier), $stamps);
            return new ApiOutput(
                ['status' => 'Queued'],
                Response::HTTP_ACCEPTED
            );
        }

        return $operationHandler->perform($apiInput);
    }


}
