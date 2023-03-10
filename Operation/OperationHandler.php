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
        private readonly OperationHandlerDiscover $operationHandlerDiscover,
        private readonly OperationBackgroundHandler $operationBackgroundHandler,
        private readonly AttributeHelper $attributeHelper,
        private readonly OperationInputValidator $operationInputValidator,
        private readonly mixed $security,
    ){ }

    public function performOperation(ApiInput $apiInput): ApiOutput
    {
        $operationHandler = $this->operationHandlerDiscover->discover($apiInput);
        $operationData = null;

        if(!empty($operationHandler->getInput())){
            $operationData = $this->operationInputValidator->validateInput($apiInput, $operationHandler->getInput());
        }

        $isGranted = $this->security->isGranted('EXECUTE_OPERATION', new OperationSubject(get_class($operationHandler), $operationHandler->getGroup()));
        if(!$isGranted) {
            throw new AccessDeniedException('Not allowed to perform this operation');
        }

        /**
         * @var IsBackground|null $attrObject
         */
        $attrObject = $this->attributeHelper->getAttr($operationHandler, IsBackground::class);
        if($attrObject){
            $userIdentifier  = $this->security->getToken()?->getUser()?->getUserIdentifier();
            $this->operationBackgroundHandler->sendToBackground($attrObject, $operationData, $operationHandler->getName(), $userIdentifier);
            return new ApiOutput(
                ['status' => 'Queued'],
                Response::HTTP_ACCEPTED
            );
        }

        return $operationHandler->perform($operationData);
    }


}
