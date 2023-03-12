<?php

namespace Ict\ApiOneEndpoint\tests\Operation;

use Ict\ApiOneEndpoint\Model\Attribute\IsBackground;
use Ict\ApiOneEndpoint\Operation\OperationBackgroundHandler;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class OperationBackgroundHandlerTest extends TestCase
{
    public function testWithoutDelay()
    {
        $bus = $this->getMock();
        $operationBackgroundHandler = new OperationBackgroundHandler($bus);
        $isBackground = new IsBackground();

        $delay = $operationBackgroundHandler->sendToBackground($isBackground, new \stdClass(), 'op', null);
        $this->assertNull($delay);
    }

    public function testWithDelay()
    {
        $bus = $this->getMock();
        $operationBackgroundHandler = new OperationBackgroundHandler($bus);
        $isBackground = new IsBackground(300);

        $delay = $operationBackgroundHandler->sendToBackground($isBackground, new \stdClass(), 'op', null);
        $this->assertEquals(300 * 1000, $delay);
    }

    /**
     * @throws Exception
     */
    private function getMock(): MessageBusInterface
    {
        $mock = $this->createMock(MessageBusInterface::class);
        $mock->method('dispatch')->willReturn(new Envelope(new \stdClass()));

        return $mock;
    }
}
