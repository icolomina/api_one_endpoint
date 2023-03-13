<?php

namespace Ict\ApiOneEndpoint\tests\Message;

use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Contract\Operation\OperationNotificationInterface;
use Ict\ApiOneEndpoint\Message\OperationMessage;
use Ict\ApiOneEndpoint\Message\OperationMessageHandler;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Ict\ApiOneEndpoint\Notification\NotificationManager;
use Ict\ApiOneEndpoint\Operation\OperationCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class OperationMessageHandlerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testOperationWithoutNotification()
    {
        $notManagerStub = $this->createMock(NotificationManager::class);
        $notManagerStub->expects($this->never())->method('notify');

        $msgHandler = new OperationMessageHandler($this->getOperations(), $notManagerStub);
        $msgHandler(new OperationMessage('', 'op2', '58744587'));
    }

    public function testOperationWithNotification()
    {

        $notManagerStub = $this->createMock(NotificationManager::class);
        $notManagerStub->expects($this->once())->method('notify');
        $notManagerStub->expects($this->once())->method('getType')->willReturn('mercure');

        $msgHandler = new OperationMessageHandler($this->getOperations(), $notManagerStub);
        $msgHandler(new OperationMessage('', 'op1', '58744587'));
    }

    private function getOperations(): OperationCollection
    {
        $operationOne = new class implements OperationInterface, OperationNotificationInterface {

            public function perform(mixed $operationData): ApiOutput { return new ApiOutput([], 200);}
            public function getName(): string { return 'op1'; }
            public function getInput(): ?string{ return null; }
            public function getGroup(): ?string{ return null; }

            public function getNotificationData(): string { return ''; }

            public function getTopic(?string $userIdentifier = null): string { return 'mytopic';  }
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
