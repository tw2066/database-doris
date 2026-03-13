<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris;

use Hyperf\Collection\Arr;

trait CatalogConnection
{
    /**
     * Run a select statement against the database.
     */
    public function select(string $query, array $bindings = [], bool $useReadPdo = true): array
    {
        if ($this->config['passthrough_sql_select'] ?? true) {
            [$query, $bindings] = $this->buildSql($query, $bindings);
            $catalog = $this->config['catalog'];
            $query = "SELECT * FROM QUERY('catalog' = '{$catalog}', 'query' = '{$query}')";
        }
        return parent::select($query, $bindings, $useReadPdo);
    }

    public function affectingStatement(string $query, array $bindings = []): int
    {
        [$query, $bindings] = $this->buildSql($query, $bindings);
        return parent::affectingStatement($query, $bindings);
    }

    protected function buildSql(string $query, array $bindings): array
    {
        if (empty($bindings)) {
            return [$query, $bindings];
        }
        $query = $this->dorisBuildSql($query, $bindings);
        $bindings = [];
        return [$query, $bindings];
    }

    protected function dorisBuildSql(string $sql, array $bindings = []): string
    {
        if (! Arr::isAssoc($bindings)) {
            $position = 0;
            foreach ($bindings as $value) {
                $position = strpos($sql, '?', $position);
                if ($position === false) {
                    break;
                }

                $value = (string) match (gettype($value)) {
                    'integer', 'double' => $value,
                    'boolean' => (int) $value,
                    // 修改
                    default => $this->getDefaultType($value),
                };
                $sql = substr_replace($sql, $value, $position, 1);
                $position += strlen($value);
            }
        }

        return $sql;
    }

    protected function getDefaultType($value): string
    {
        return sprintf('"%s"', addslashes($value));
    }
}
