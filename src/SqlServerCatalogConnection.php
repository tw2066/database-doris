<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris;

use Hyperf\Database\Doris\Concerns\CatalogManagesTransactions;
use Hyperf\Database\Doris\Query\Grammars\SqlServerCatalogGrammar;
use Hyperf\Database\Sqlsrv\Query\Grammars\SqlServerGrammar;
use Hyperf\Database\Sqlsrv\SqlServerConnection;

class SqlServerCatalogConnection extends SqlServerConnection
{
    use CatalogConnection;
    use CatalogManagesTransactions;

    protected function getDefaultQueryGrammar(): SqlServerGrammar
    {
        /* @phpstan-ignore-next-line */
        return $this->withTablePrefix(new SqlServerCatalogGrammar());
    }
}
