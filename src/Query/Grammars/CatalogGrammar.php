<?php

declare(strict_types=1);

namespace Hyperf\Database\Doris\Query\Grammars;

use Hyperf\Database\Query\Builder;

trait CatalogGrammar
{
    /**
     * Compile a select query into SQL.
     */
    public function compileSelect(Builder $query): string
    {
        $config = $this->getConfig($query);
        // sql 透传
        $passthroughSQL = $config['passthrough_sql_select'] ?? true;
        if (! str_contains($query->from, '.')) {
            if ($passthroughSQL) {
                $query->from = $config['database'] . '.' . $query->from;
            } else {
                $query->from = $config['catalog'] . '.' . $config['database'] . '.' . $query->from;
            }
        }
        return parent::compileSelect($query);
    }

    public function compileUpdate(Builder $query, array $values): string
    {
        $config = $this->getConfig($query);
        if (! str_contains($query->from, '.')) {
            $query->from = $config['database'] . '.' . $query->from;
        }
        return parent::compileUpdate($query, $values);
    }

    public function compileDelete(Builder $query): string
    {
        $config = $this->getConfig($query);
        if (! str_contains($query->from, '.')) {
            $query->from = $config['database'] . '.' . $query->from;
        }
        return parent::compileDelete($query);
    }

    /**
     * Compile an insert statement into SQL.
     */
    public function compileInsert(Builder $query, array $values): string
    {
        $config = $this->getConfig($query);
        // sql 透传
        $passthroughSQL = $config['passthrough_sql_insert'] ?? true;
        if (! str_contains($query->from, '.')) {
            if ($passthroughSQL) {
                $query->from = $config['database'] . '.' . $query->from;
            } else {
                $query->from = $config['catalog'] . '.' . $config['database'] . '.' . $query->from;
            }
        }
        return parent::compileInsert($query, $values);
    }

    protected function getConfig(Builder $query)
    {
        $getResponseBuilder = function () {
            return $this->config;
        };
        return $getResponseBuilder->call($query->getConnection());
    }
}
