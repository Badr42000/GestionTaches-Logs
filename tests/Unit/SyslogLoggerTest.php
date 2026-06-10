<?php

namespace Tests\Unit;

use App\Service\LoggerInterface;
use App\Service\SyslogLogger;
use PHPUnit\Framework\TestCase;

class SyslogLoggerTest extends TestCase
{
    private SyslogLogger $logger;

    protected function setUp(): void
    {
        $this->logger = new SyslogLogger();
    }

    public function testImplementsLoggerInterface(): void
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->logger);
    }

    public function testSendMethodExists(): void
    {
        $this->assertTrue(method_exists($this->logger, 'send'));
    }

    public function testSendAcceptsValidSeverities(): void
    {
        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TEST',
            'message' => 'test',
        ]));

        $this->expectNotToPerformAssertions();
    }
}
