<?php

namespace Tests\Unit;

use App\Model\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    private \PDO $pdo;
    private User $user;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->user = new User($this->pdo);
    }

    public function testUserExtendsAbstractModel(): void
    {
        $this->assertInstanceOf(\App\Model\AbstractModel::class, $this->user);
    }

    public function testFindByUsernameReturnsUser(): void
    {
        $expectedUser = [
            'id' => 1,
            'username' => 'admin',
            'password' => '$2y$10$hash123',
        ];

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['admin'])
            ->willReturn(true);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedUser);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM users WHERE username = ?'))
            ->willReturn($stmt);

        $result = $this->user->findByUsername('admin');

        $this->assertIsArray($result);
        $this->assertEquals('admin', $result['username']);
    }

    public function testFindByUsernameReturnsFalseWhenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['inexistant'])
            ->willReturn(true);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $result = $this->user->findByUsername('inexistant');

        $this->assertFalse($result);
    }

    public function testExistsReturnsTrueWhenUserFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['admin'])
            ->willReturn(true);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT id FROM users WHERE username = ?'))
            ->willReturn($stmt);

        $result = $this->user->exists('admin');

        $this->assertTrue($result);
    }

    public function testExistsReturnsFalseWhenUserNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['inconnu'])
            ->willReturn(true);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $result = $this->user->exists('inconnu');

        $this->assertFalse($result);
    }

    public function testCreateReturnsInsertedId(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['newuser', '$2y$10$hashvalue'])
            ->willReturn(true);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO users'))
            ->willReturn($stmt);
        $this->pdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('3');

        $id = $this->user->create('newuser', '$2y$10$hashvalue');

        $this->assertSame(3, $id);
    }
}
