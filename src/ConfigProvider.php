<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris;

use Hyperf\Database\Doris\Connectors\DorisMysqlConnector;
use Hyperf\Database\Doris\Listener\RegisterConnectionListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'db.connector.doris' => DorisMysqlConnector::class,
                'db.connector.doris_catalog' => DorisMysqlConnector::class,
                'db.connector.doris_catalog_mysql' => DorisMysqlConnector::class,
                'db.connector.doris_catalog_pgsql' => DorisMysqlConnector::class,
                'db.connector.doris_catalog_sqlsrv' => DorisMysqlConnector::class,
            ],
            'listeners' => [
                RegisterConnectionListener::class,
            ],
        ];
    }
}
