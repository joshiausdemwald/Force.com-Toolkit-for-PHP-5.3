<?php
/**
 * Copyright (C) 2012 code mitte GmbH - Zeughausstr. 28-38 - 50667 Cologne/Germany
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so, subject
 * to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Codemitte\Sfdc\Soql\Query;

use Codemitte\Sfdc\Soql\Query\Expression\ExpressionInterface;
use Codemitte\Sfdc\Soql\Query\Expression\ExpressionBuilderInterface;

class QueryBuilder
{
    /**
     * @var \Codemitte\Sfdc\Soql\Query\Expression\ExpressionBuilderInterface $expressionBuilder
     */
    private $expressionBuilder;

    /**
     * @var array
     */
    private $soqlParts = array(
        'select'  => array(),
        'from'    => array(),
        'where'   => null,
        'groupBy' => array(),
        'having'  => null,
        'orderBy' => array()
    );

    /**
     * @var array
     */
    private $params;

    /**
     * @var integer
     */
    private $limit;

    /**
     * @var integer
     */
    private $offset;

    /**
     * Constructor.
     */
    public function __construct(ExpressionBuilderInterface $expressionBuilder)
    {
        $this->expressionBuilder = $expressionBuilder;

        $this->params = array();
    }

    /**
     * @return Expression\ExpressionBuilderInterface
     */
    public function getExpressionBuilder()
    {
        return $this->expressionBuilder;
    }

    /**
     * @param $name
     * @param $part
     * @param bool $append
     * @return QueryBuilder
     */
    public function add($name, $part, $append = false)
    {
        if(is_string($part))
        {
            $part = array($part);
        }

        if($append && isset($this->soqlParts[$name]))
        {
            $this->soqlParts[$name]->addAll($part);
        }
        else
        {
            $this->soqlParts[$name] = $this->expressionBuilder->build($name, $part);
        }

        return $this;
    }

    /**
     * @return string
     * @throws
     */
    private function getSoql()
    {
        $query = 'SELECT ' . implode(', ', $this->sqlParts['select']) . ' FROM ';

        $fromClauses = array();

        // Loop through all FROM clauses
        foreach ($this->sqlParts['from'] as $from) {
            $fromClause = $from['table'] . ' ' . $from['alias'];

            if (isset($this->sqlParts['join'][$from['alias']])) {
                foreach ($this->sqlParts['join'][$from['alias']] as $join) {
                    $fromClause .= ' ' . strtoupper($join['joinType'])
                        . ' JOIN ' . $join['joinTable'] . ' ' . $join['joinAlias']
                        . ' ON ' . ((string) $join['joinCondition']);
                }
            }

            $fromClauses[$from['alias']] = $fromClause;
        }

        // loop through all JOIN clasues for validation purpose
        foreach ($this->sqlParts['join'] as $fromAlias => $joins) {
            if ( ! isset($fromClauses[$fromAlias]) ) {
                throw QueryException::unknownFromAlias($fromAlias, array_keys($fromClauses));
            }
        }

        $query .= implode(', ', $fromClauses)
            . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '')
            . ($this->sqlParts['groupBy'] ? ' GROUP BY ' . implode(', ', $this->sqlParts['groupBy']) : '')
            . ($this->sqlParts['having'] !== null ? ' HAVING ' . ((string) $this->sqlParts['having']) : '')
            . ($this->sqlParts['orderBy'] ? ' ORDER BY ' . implode(', ', $this->sqlParts['orderBy']) : '');

        return ($this->maxResults === null && $this->firstResult == null)
            ? $query
            : $this->connection->getDatabasePlatform()->modifyLimitQuery($query, $this->maxResults, $this->firstResult);
    }

    /**
     *
     * @param $queryPartName
     * @return boolean
     */
    protected function isSoqlPartMultiple($queryPartName)
    {
       return is_array($this->soqlParts);
    }

}
