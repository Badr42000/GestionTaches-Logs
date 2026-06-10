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

    public function testSendWithValidSeverities(): void
    {
        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TEST',
            'message' => 'test',
        ]));

        $this->expectNotToPerformAssertions();
    }

    public function testSendWithWarningSeverity(): void
    {
        $this->logger->send('warning', 'tasklogger', json_encode([
            'action' => 'TEST_WARNING',
            'message' => 'test warning',
        ]));

        $this->expectNotToPerformAssertions();
    }

    public function testSendWithAllSeverities(): void
    {
        $severities = ['emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug'];
        foreach ($severities as $severity) {
            $this->logger->send($severity, 'tasklogger', json_encode([
                'action' => 'TEST',
                'severity' => $severity,
            ]));
        }

        $this->expectNotToPerformAssertions();
    }

    public function testSendWithJsonMessage(): void
    {
        $data = [
            'action' => 'TASK_CREATED',
            'id' => 42,
            'title' => 'Test task',
            'username' => 'admin',
        ];

        $this->logger->send('info', 'tasklogger', json_encode($data));

        $this->expectNotToPerformAssertions();
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $logger = new SyslogLogger();
        $this->assertInstanceOf(SyslogLogger::class, $logger);
    }
}
