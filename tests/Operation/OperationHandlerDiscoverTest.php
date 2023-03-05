<?php

namespace Ict\ApiOneEndpoint\tests\Operation;

use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Exception\OperationNotDefinedException;
use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Ict\ApiOneEndpoint\Operation\OperationCollection;
use Ict\ApiOneEndpoint\Operation\OperationHandlerDiscover;
use PHPUnit\Framework\TestCase;

class OperationHandlerDiscoverTest extends TestCase
{
    public function testExisingOperation()
    {
        $operationsCollection     = $this->getOperations();
        $operationHandlerDiscover = new OperationHandlerDiscover($operationsCollection);
        $apiInput = new ApiInput();
        $apiInput->setOperation('op1');
        $apiInput->setData([]);

        $this->assertInstanceOf(OperationInterface::class, $operationHandlerDiscover->discover($apiInput));
    }

    public function testNoExisingOperation()
    {
        $this->expectException(OperationNotDefinedException::class);

        $operationsCollection     = $this->getOperations();
        $operationHandlerDiscover = new OperationHandlerDiscover($operationsCollection);
        $apiInput = new ApiInput();
        $apiInput->setOperation('op3');
        $apiInput->setData([]);

        $this->assertInstanceOf(OperationInterface::class, $operationHandlerDiscover->discover($apiInput));
    }

    private function getOperations(): OperationCollection
    {
        $operationOne = new class implements OperationInterface {

            public function perform(mixed $operationData): ApiOutput { return new ApiOutput([], 200);}
            public function getName(): string { return 'op1'; }
            public function getInput(): ?string{ return null; }
            public function getGroup(): ?string{ return null; }
        };

        $operationTwo = new class implements OperationInterface {

            public function perform(mixed $operationData): ApiOutput { return new ApiOutput([], 200);}
            public function getName(): string { return 'op2'; }
            public function getInput(): ?string{ return null; }
            public function getGroup(): ?string{ return null; }
        };

        return new OperationCollection([
            $operationOne,
            $operationTwo
        ]);
    }

}
