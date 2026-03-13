<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris\Query\Grammars;

use Hyperf\Database\Oracle\Query\Grammars\OracleGrammar;

class OracleCatalogGrammar extends OracleGrammar
{
    use CatalogGrammar;
}
