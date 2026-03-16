<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris;

use Hyperf\Database\Doris\Concerns\CatalogManagesTransactions;
use Hyperf\Database\Doris\Query\Grammars\OracleCatalogGrammar;
use Hyperf\Database\Oracle\OracleSqlConnection;
use Hyperf\Database\Oracle\Query\Grammars\OracleGrammar;

class OracleSqlCatalogConnection extends OracleSqlConnection
{
    use CatalogConnection;
    use CatalogManagesTransactions;

    protected function getDefaultQueryGrammar(): OracleGrammar
    {
        /* @phpstan-ignore-next-line */
        return $this->withTablePrefix(new OracleCatalogGrammar());
    }
}
