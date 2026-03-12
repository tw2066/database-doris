<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris\Connectors;

use Hyperf\Database\Connectors\MySqlConnector;
use PDO;

class DorisMysqlConnector extends MySqlConnector
{
    /**
     * Establish a database connection.
     *
     * @return PDO
     */
    public function connect(array $config)
    {
        $dorisConfig = $config;

        if (! empty($config['catalog'])){
            unset($dorisConfig['database']);
        }

        $dsn = $this->getDsn($dorisConfig);

        $config['options'][PDO::ATTR_EMULATE_PREPARES] = true;
        $options = $this->getOptions($config);

        // We need to grab the PDO options that should be used while making the brand
        // new connection instance. The PDO options control various aspects of the
        // connection's behavior, and some might be specified by the developers.
        $connection = $this->createConnection($dsn, $config, $options);

         if (! empty($dorisConfig['database'])) {
            $connection->exec("use `{$dorisConfig['database']}`;");
         }

        $this->configureEncoding($connection, $config);

        // Next, we will check to see if a timezone has been specified in this config
        // and if it has we will issue a statement to modify the timezone with the
        // database. Setting this DB timezone is an optional configuration item.
        $this->configureTimezone($connection, $config);

        $this->setModes($connection, $config);

        return $connection;
    }
}
