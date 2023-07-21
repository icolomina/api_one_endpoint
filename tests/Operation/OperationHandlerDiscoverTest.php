<?php

namespace Ict\ApiOneEndpoint\tests\Operation;

use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Exception\ContextOperationNotMatchException;
use Ict\ApiOneEndpoint\Exception\ContextOperationRequiredException;
use Ict\ApiOneEndpoint\Exception\OperationNotDefinedException;
use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Ict\ApiOneEndpoint\Model\Api\Context;
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

        $this->assertInstanceOf(OperationInterface::class, $operationHandlerDiscover->discover($apiInput, new Context()));
    }

    public function testNoExisingOperation()
    {
        $this->expectException(OperationNotDefinedException::class);

        $operationsCollection     = $this->getOperations();
        $operationHandlerDiscover = new OperationHandlerDiscover($operationsCollection);
        $apiInput = new ApiInput();
        $apiInput->setOperation('op3');
        $apiInput->setData([]);

        $operationHandlerDiscover->discover($apiInput, new Context());
    }

    public function testContextRequired()
    {
        $this->expectException(ContextOperationRequiredException::class);

        $operationsCollection     = $this->getOperationsWithContext();
        $operationHandlerDiscover = new OperationHandlerDiscover($operationsCollection);
        $apiInput = new ApiInput();
        $apiInput->setOperation('op1');
        $apiInput->setData([]);

        $operationHandlerDiscover->discover($apiInput, new Context());
    }

    public function testInvalidContext()
    {
        $this->expectException(ContextOperationNotMatchException::class);

        $operationsCollection     = $this->getOperationsWithContext();
        $operationHandlerDiscover = new OperationHandlerDiscover($operationsCollection);
        $apiInput = new ApiInput();
        $apiInput->setOperation('op1');
        $apiInput->setData([]);

        $operationHandlerDiscover->discover($apiInput, new Context('ctx2'));
    }

    public function testValidContext()
    {
        $operationsCollection     = $this->getOperationsWithContext();
        $operationHandlerDiscover = new OperationHandlerDiscover($operationsCollection);
        $apiInput = new ApiInput();
        $apiInput->setOperation('op1');
        $apiInput->setData([]);

        $this->assertInstanceOf(OperationInterface::class, $operationHandlerDiscover->discover($apiInput, new Context('ctx1')));
    }

    private function getOperations(): OperationCollection
    {
        $operationOne = new class implements OperationInterface {

            public function perform(mixed $operationData): ApiOutput { return new ApiOutput([], 200);}
            public function getName(): string { return 'op1'; }
            public function getInput(): ?string{ return null; }
            public function getGroup(): ?string{ return null; }
            public function getContext(): ?array{ return null; }
        };

        $operationTwo = new class implements OperationInterface {

            public function perform(mixed $operationData): ApiOutput { return new ApiOutput([], 200);}
            public function getName(): string { return 'op2'; }
            public function getInput(): ?string{ return null; }
            public function getGroup(): ?string{ return null; }
            public function getContext(): ?array{ return null; }
        };

        return new OperationCollection([
            $operationOne,
            $operationTwo
        ]);
    }

    private function getOperationsWithContext(): OperationCollection
    {
        $operationOne = new class implements OperationInterface {

            public function perform(mixed $operationData): ApiOutput { return new ApiOutput([], 200);}
            public function getName(): string { return 'op1'; }
            public function getInput(): ?string{ return null; }
            public function getGroup(): ?string{ return null; }
            public function getContext(): ?array{ return ['ctx1']; }
        };

        $operationTwo = new class implements OperationInterface {

            public function perform(mixed $operationData): ApiOutput { return new ApiOutput([], 200);}
            public function getName(): string { return 'op2'; }
            public function getInput(): ?string{ return null; }
            public function getGroup(): ?string{ return null; }
            public function getContext(): ?array{ return ['ctx2']; }
        };

        return new OperationCollection([
            $operationOne,
            $operationTwo
        ]);
    }
}
