<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris;

use Hyperf\Database\Doris\Concerns\CatalogManagesTransactions;
use Hyperf\Database\Doris\Query\Grammars\PostgresCatalogGrammar;
use Hyperf\Database\PgSQL\PostgreSqlConnection;
use Hyperf\Database\PgSQL\Query\Grammars\PostgresGrammar;

class PostgresSqlCatalogConnection extends PostgreSqlConnection
{
    use CatalogConnection;
    use CatalogManagesTransactions;

    protected function getDefaultQueryGrammar(): PostgresGrammar
    {
        /* @phpstan-ignore-next-line */
        return $this->withTablePrefix(new PostgresCatalogGrammar());
    }
}
