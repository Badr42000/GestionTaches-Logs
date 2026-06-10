<?php

namespace Tests\Unit;

use App\Model\User;
use App\Core\Database;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function testUserExtendsAbstractModel(): void
    {
        $pdo = Database::getInstance();
        $user = new User($pdo);

        $this->assertInstanceOf(\App\Model\AbstractModel::class, $user);
    }

    public function testUserHasRequiredMethods(): void
    {
        $pdo = Database::getInstance();
        $user = new User($pdo);

        $this->assertTrue(method_exists($user, 'findByUsername'));
        $this->assertTrue(method_exists($user, 'exists'));
        $this->assertTrue(method_exists($user, 'create'));
    }
}
