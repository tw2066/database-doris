<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris;

use Hyperf\Database\Doris\Concerns\CatalogManagesTransactions;
use Hyperf\Database\Doris\Query\Grammars\MysqlCatalogGrammar;
use Hyperf\Database\MySqlConnection;
use Hyperf\Database\Query\Grammars\MySqlGrammar as QueryGrammar;

class MysqlCatalogConnection extends MySqlConnection
{
    use CatalogConnection;
    use CatalogManagesTransactions;

    /**
     * Get the default query grammar instance.
     *
     * @return MysqlCatalogGrammar
     */
    protected function getDefaultQueryGrammar(): QueryGrammar
    {
        /* @phpstan-ignore-next-line */
        return $this->withTablePrefix(new MysqlCatalogGrammar());
    }
}
