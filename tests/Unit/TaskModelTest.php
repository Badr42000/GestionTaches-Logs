<?php

namespace Tests\Unit;

use App\Model\Task;
use App\Core\Database;
use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase
{
    public function testTaskExtendsAbstractModel(): void
    {
        $pdo = Database::getInstance();
        $task = new Task($pdo);

        $this->assertInstanceOf(\App\Model\AbstractModel::class, $task);
    }

    public function testTaskHasRequiredMethods(): void
    {
        $pdo = Database::getInstance();
        $task = new Task($pdo);

        $this->assertTrue(method_exists($task, 'findAll'));
        $this->assertTrue(method_exists($task, 'findById'));
        $this->assertTrue(method_exists($task, 'create'));
        $this->assertTrue(method_exists($task, 'update'));
        $this->assertTrue(method_exists($task, 'delete'));
        $this->assertTrue(method_exists($task, 'updateStatus'));
    }
}
