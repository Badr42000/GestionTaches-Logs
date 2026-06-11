<?php

namespace Dashboard\Model;

use PDO;

abstract class AbstractModel
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
