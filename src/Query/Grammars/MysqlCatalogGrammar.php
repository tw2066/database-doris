<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris\Query\Grammars;

use Hyperf\Database\Query\Grammars\MySqlGrammar;

class MysqlCatalogGrammar extends MySqlGrammar
{
    use CatalogGrammar;
}
