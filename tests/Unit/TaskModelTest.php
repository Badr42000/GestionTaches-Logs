<?php

namespace Tests\Unit;

use App\Model\Task;
use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase
{
    private \PDO $pdo;
    private Task $task;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->task = new Task($this->pdo);
    }

    public function testTaskExtendsAbstractModel(): void
    {
        $this->assertInstanceOf(\App\Model\AbstractModel::class, $this->task);
    }

    public function testFindAllReturnsArray(): void
    {
        $expectedTasks = [
            ['id' => 1, 'title' => 'Tâche 1', 'status' => 'todo'],
            ['id' => 2, 'title' => 'Tâche 2', 'status' => 'done'],
        ];

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedTasks);

        $this->pdo->expects($this->once())
            ->method('query')
            ->with($this->stringContains('SELECT * FROM tasks'))
            ->willReturn($stmt);

        $result = $this->task->findAll();

        $this->assertCount(2, $result);
        $this->assertEquals('Tâche 1', $result[0]['title']);
    }

    public function testFindByIdReturnsTask(): void
    {
        $expectedTask = ['id' => 1, 'title' => 'Tâche test', 'status' => 'todo'];

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1])
            ->willReturn(true);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedTask);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM tasks WHERE id = ?'))
            ->willReturn($stmt);

        $result = $this->task->findById(1);

        $this->assertIsArray($result);
        $this->assertEquals('Tâche test', $result['title']);
    }

    public function testFindByIdReturnsFalseWhenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([999])
            ->willReturn(true);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $result = $this->task->findById(999);

        $this->assertFalse($result);
    }

    public function testCreateReturnsInsertedId(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['Nouvelle tâche', 'Description', 'haute', 'admin'])
            ->willReturn(true);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO tasks'))
            ->willReturn($stmt);
        $this->pdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('42');

        $id = $this->task->create('Nouvelle tâche', 'Description', 'haute', 'admin');

        $this->assertSame(42, $id);
    }

    public function testUpdateExecutesPreparedStatement(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['Titre modifié', 'Desc modifiée', 'basse', 1])
            ->willReturn(true);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('UPDATE tasks SET'))
            ->willReturn($stmt);

        $this->task->update(1, 'Titre modifié', 'Desc modifiée', 'basse');

        $this->expectNotToPerformAssertions();
    }

    public function testDeleteExecutesPreparedStatement(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([5])
            ->willReturn(true);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('DELETE FROM tasks'))
            ->willReturn($stmt);

        $this->task->delete(5);
    }

    public function testUpdateStatusExecutesPreparedStatement(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['done', 1])
            ->willReturn(true);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('UPDATE tasks SET status'))
            ->willReturn($stmt);

        $this->task->updateStatus(1, 'done');
    }
}
