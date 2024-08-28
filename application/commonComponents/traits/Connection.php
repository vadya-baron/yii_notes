<?php

declare(strict_types=1);


namespace commonComponents\traits;

use Throwable;
use yii\db\Connection as YiConnection;

trait Connection
{
    protected YiConnection $connection;

    public function setConnection(YiConnection $connection): void
    {
        $this->connection = $connection;
    }

    protected function checkConnection(): bool
    {
        try {
            $this->connection->open();
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}