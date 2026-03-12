<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris\Query\Grammars;

use Hyperf\Database\Sqlsrv\Query\Grammars\SqlServerGrammar;

class SqlServerCatalogGrammar extends SqlServerGrammar
{
    use CatalogGrammar;
}
