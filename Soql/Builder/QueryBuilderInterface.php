<?php
namespace Codemitte\ForceToolkit\Soql\Builder;

use
    \Codemitte\ForceToolkit\Soql\AST;

interface QueryBuilderInterface
{
    /**
     * Method to support legacy calls to raw "query()"-method
     * on client instance.
     *
     * @param $soql
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResult
     */
    public function query($soql, array $parameters = array());

    /**
     * Like query(), but does not call execute in one step, supporting
     * chaining.
     *
     * @param $soql
     * @param array $parameters
     * @return \Codemitte\ForceToolkit\Soql\Builder\QueryBuilder
     */
    public function prepareStatement($soql, array $parameters = array());

    /**
     * @param $soql
     * @return \Codemitte\ForceToolkit\Soql\Builder\QueryBuilder
     */
    public function select($soql);

    /**
     * @param $soql
     * @return \Codemitte\ForceToolkit\Soql\Builder\QueryBuilder
     */
    public function addSelect($soql);

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function from($soql);

    /**
     * @param LogicalJunction|string $soql
     * @param array $parameters
     * @return QueryBuilder
     */
    public function where($soql, array $parameters = array());

    /**
     * @param LogicalJunction|string $soql
     * @return QueryBuilder
     */
    public function withDataCategory($soql);

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function andWithDataCategory($soql);

    /**
     * @param LogicalJunction|$soql
     * @return QueryBuilder
     */
    public function addWithDataCategory($soql);

    public function groupBy($soql);

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function addGroupBy($soql);
    /**
     * @param bool $groupByCube
     * @return QueryBuilder
     */
    public function setGroupByCube($groupByCube = true);

    /**
     * @param bool $groupByRollup
     * @return QueryBuilder
     */
    public function setGroupByRollup($groupByRollup = true);

    /**
     * @param LogicalJunction|$soql
     * @return QueryBuilder
     */
    public function having($soql);

    /**
     * @param $soql
     * @param null $dir
     * @param null $nulls
     * @return QueryBuilder
     */
    public function orderBy($soql, $dir = null, $nulls = null);

    /**
     * @param $soql
     * @param null $dir
     * @param null $nulls
     * @return QueryBuilder
     */
    public function addOrderBy($soql, $dir = null, $nulls = null);

    /**
     * @param $limit
     * @return QueryBuilder
     */
    public function limit($limit);

    /**
     * @param $offset
     * @return QueryBuilder
     */
    public function offset($offset);

    /**
     * @param array $parameters
     * @return QueryBuilder
     */
    public function bind(array $parameters);

    /**
     * @return string
     */
    public function getSoql();

    /**
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResult
     */
    public function execute(array $parameters = array());

    /**
     * @param array $parameters
     * @param null $default
     * @return \Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface
     */
    public function getSingleResult(array $parameters = array(), $default = null);

    /**
     * Proxy for getSingleResult()
     * @param array $parameters
     * @param null $default
     * @return \Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface
     */
    public function fetchOne(array $parameters = array(), $default = null);

    /**
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResultCollection
     */
    public function getResult($parameters = array());

    /**
     * Proxy for getResult()
     *
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResultCollection
     */
    public function fetch($parameters = array());

    /**
     * @param array $parameters
     * @return int
     */
    public function count(array $parameters = array());

    /**
     * @return ExpressionBuilderInterface
     */
    public function whereExpr();

    /**
     * @return ExpressionBuilderInterface
     */
    public function havingExpr();

    /**
     * @param int $context: One of the ExpressionBuilderInterface::CONTEXT_* constants
     * @return ExpressionBuilderInterface
     */
    public function newExpr($context);

}
