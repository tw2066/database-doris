<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Connection;
use Hyperf\Database\Doris\MysqlCatalogConnection;
use Hyperf\Database\Doris\OracleSqlCatalogConnection;
use Hyperf\Database\Doris\PostgresSqlCatalogConnection;
use Hyperf\Database\Doris\SqlServerCatalogConnection;
use Hyperf\Database\MySqlConnection;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Psr\Container\ContainerInterface;

class RegisterConnectionListener implements ListenerInterface
{
    /**
     * Create a new connection factory instance.
     */
    public function __construct(protected ContainerInterface $container, private ConfigInterface $config)
    {
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    /**
     * Register sqlite connection.
     */
    public function process(object $event): void
    {
        $databases = $this->config->get('databases');
        foreach ($databases as $name => $database) {
            if ($database['driver'] == 'doris_catalog') {
                ! isset($database['passthrough_sql_select']) && $this->config->set("databases.{$name}.passthrough_sql_select", false);
            }
            if ($database['driver'] == 'doris_catalog_mysql') {
                ! isset($database['passthrough_sql_insert']) && $this->config->set("databases.{$name}.passthrough_sql_insert", false);
            }
        }

        Connection::resolverFor('doris', function ($connection, $database, $prefix, $config) {
            // 使用mysql连接协议,和mysql查询协议一致
            return new MySqlConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('doris_catalog', function ($connection, $database, $prefix, $config) {
            return new MysqlCatalogConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('doris_catalog_mysql', function ($connection, $database, $prefix, $config) {
            return new MysqlCatalogConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('doris_catalog_pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresSqlCatalogConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('doris_catalog_sqlsrv', function ($connection, $database, $prefix, $config) {
            return new SqlServerCatalogConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('doris_catalog_oracle', static function ($connection, $database, $prefix, $config) {
            return new OracleSqlCatalogConnection($connection, $database, $prefix, $config);
        });
    }
}
