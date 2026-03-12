<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris\Query\Grammars;

use Hyperf\Database\PgSQL\Query\Grammars\PostgresGrammar;

class PostgresCatalogGrammar extends PostgresGrammar
{
    use CatalogGrammar;
}
