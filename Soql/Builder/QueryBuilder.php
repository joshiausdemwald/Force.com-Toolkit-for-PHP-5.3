<?php
namespace Codemitte\ForceToolkit\Soql\Builder;

use Codemitte\ForceToolkit\Soql\Parser\QueryParser;
use Codemitte\ForceToolkit\Soql\Renderer\QueryRenderer;
use Codemitte\ForceToolkit\Soql\AST\Query;
use Codemitte\ForceToolkit\Soap\Client\APIInterface;
use Codemitte\Soap\Mapping\GenericResultCollection;

class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var \Codemitte\ForceToolkit\Soql\AST\Query
     */
    private $query;

    /**
     * @var \Codemitte\ForceToolkit\Soql\Parser\QueryParser
     */
    private $parser;

    /**
     * @var \Codemitte\ForceToolkit\Soql\Renderer\QueryRenderer
     */
    private $renderer;

    /**
     * @var APIInterface
     */
    private $client;

    /**
     * @var array<array<mixed>>
     */
    private $parameters;

    /**
     * @param APIInterface $client
     * @param \Codemitte\ForceToolkit\Soql\Parser\QueryParser $parser
     * @param QueryRenderer $renderer
     */
    public function __construct(APIInterface $client, QueryParser $parser, QueryRenderer $renderer)
    {
        $this->client = $client;

        $this->parser = $parser;

        $this->renderer = $renderer;
    }

    /**
     * Method to support legacy calls to raw "query()"-method
     * on client instance.
     *
     * @param $soql
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResult
     */
    public function query($soql, array $parameters = array())
    {
        return $this->prepareStatement($soql)->execute($parameters);
    }

    /**
     * Like query(), but does not call execute in one step, supporting
     * chaining.
     *
     * @param $soql
     * @param array $parameters
     * @return \Codemitte\ForceToolkit\Soql\Builder\QueryBuilder
     */
    public function prepareStatement($soql, array $parameters = array())
    {

        $this->query = $this->parser->parse($soql);

        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param $soql
     * @return \Codemitte\ForceToolkit\Soql\Builder\QueryBuilder
     */
    public function select($soql)
    {
        $this->query = new Query();

        $this->parameters = array();

        $p = new \Codemitte\ForceToolkit\Soql\AST\SelectPart();

        $this->query->setSelectPart($p);

        return $this->addSelect($soql);
    }

    /**
     * @param $soql
     * @return \Codemitte\ForceToolkit\Soql\Builder\QueryBuilder
     */
    public function addSelect($soql)
    {
        if(null === $this->query->getSelectPart())
        {
            return $this->select($soql);
        }

        $this->query->getSelectPart()->addSelectFields($this->parser->parseSelectSoql($soql));

        return $this;
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function from($soql)
    {
        $this->query->setFromPart($this->parser->parseFromSoql($soql));

        return $this;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface|string $soql
     * @param array $parameters
     * @return QueryBuilder
     */
    public function where($soql, array $parameters = array())
    {
        // STARTOVER ...
        $this->query->setWherePart(null);

        return $this->addWhere($soql, null, false, $parameters);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface|string $soql
     * @param array $parameters
     * @return QueryBuilder
     */
    public function whereNot($soql, array $parameters = array())
    {
        // STARTOVER ...
        $this->query->setWherePart(null);

        return $this->addWhere($soql, null, true, $parameters);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface|string $soql
     * @param array $parameters
     * @return QueryBuilder
     */
    public function andWhere($soql, array $parameters = array())
    {
        return $this->addWhere($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_AND, false, $parameters);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface|string $soql
     * @param array $parameters
     * @return QueryBuilder
     */
    public function andWhereNot($soql, array $parameters = array())
    {
        return $this->addWhere($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_AND, true, $parameters);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface|string $soql
     * @param array $parameters
     * @return QueryBuilder
     */
    public function orWhere($soql, array $parameters = array())
    {
        return $this->addWhere($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_OR, false, $parameters);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface|string $soql
     * @param array $parameters
     * @return QueryBuilder
     */
    public function orWhereNot($soql, array $parameters = array())
    {
        return $this->addWhere($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_OR, true, $parameters);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface|string $soql
     * @param string $operator
     * @param bool $isNot
     * @param array $parameters
     * @return QueryBuilder
     */
    public function addWhere($soql, $operator = null, $isNot = false, array $parameters = array())
    {
        $this->mergeParameters($parameters);

        if(null === $this->query->getWherePart())
        {
            $this->query->setWherePart(
                new \Codemitte\ForceToolkit\Soql\AST\WherePart(
                    new \Codemitte\ForceToolkit\Soql\AST\LogicalGroup()));
        }
        elseif(null === $operator)
        {
            $operator = \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_AND;
        }

        $junction = new \Codemitte\ForceToolkit\Soql\AST\LogicalJunction();
        $junction->setOperator($operator);
        $junction->setIsNot($isNot);

        if($soql instanceof \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface)
        {
            $junction->setCondition($soql);
        }
        else
        {
            $group = new \Codemitte\ForceToolkit\Soql\AST\LogicalGroup();
            $group->addAll($this->parser->parseWhereSoql($soql));
            $junction->setCondition($group);
        }

        $this->query->getWherePart()->getLogicalGroup()->add($junction);

        return $this;
    }

    /**
     * @param LogicalJunction|string $soql
     * @return QueryBuilder
     */
    public function withDataCategory($soql)
    {
        $this->query->setWithPart(null);

        return $this->addWithDataCategory($soql);
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function andWithDataCategory($soql)
    {
        $g = new \Codemitte\ForceToolkit\Soql\AST\LogicalGroup();

        $g->addAll($this->parser->parseWithSoql($soql));

        $junction = new \Codemitte\ForceToolkit\Soql\AST\LogicalJunction();
        $junction->setOperator(\Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_AND);
        $junction->setCondition($g);

        $this->addWithDataCategory($junction);

        return $this;
    }

    /**
     * @param LogicalJunction|$soql
     * @return QueryBuilder
     */
    public function addWithDataCategory($soql)
    {
        if(null === $this->query->getWithPart())
        {
            $this->query->setWithPart(new \Codemitte\ForceToolkit\Soql\AST\WithPart(new \Codemitte\ForceToolkit\Soql\AST\LogicalGroup()));
        }

        if($soql instanceof \Codemitte\ForceToolkit\Soql\AST\LogicalJunction)
        {
            $this->query->getWithPart()->getLogicalGroup()->add($soql);
        }
        else
        {
            $this->query->getWithPart()->getLogicalGroup()->addAll($this->parser->parseWithSoql($soql));
        }
        return $this;
    }

    public function groupBy($soql)
    {
        $this->query->setGroupPart(new \Codemitte\ForceToolkit\Soql\AST\GroupByExpression());

        return $this->addGroupBy($soql);
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function addGroupBy($soql)
    {
        if(null === $this->query->getGroupPart())
        {
            return $this->groupBy($soql);
        }

        $this->query->getGroupPart()->addGroupFields($this->parser->parseGroupSoql($soql));

        return $this;
    }

    /**
     * @param bool $groupByCube
     * @return QueryBuilder
     */
    public function setGroupByCube($groupByCube = true)
    {
        if(null === $this->query->getGroupPart())
        {
            $this->query->setGroupPart($g = new \Codemitte\ForceToolkit\Soql\AST\GroupByExpression());
        }
        $this->query->getGroupPart()->setIsCube($groupByCube);

        return $this;
    }

    /**
     * @param bool $groupByRollup
     * @return QueryBuilder
     */
    public function setGroupByRollup($groupByRollup = true)
    {
        if(null === $this->query->getGroupPart())
        {
            $this->query->setGroupPart($g = new \Codemitte\ForceToolkit\Soql\AST\GroupByExpression());
        }
        $this->query->getGroupPart()->setIsRollup($groupByRollup);

        return $this;
    }

    /**
     * @param LogicalJunction|$soql
     * @return QueryBuilder
     */
    public function having($soql)
    {
        $this->query->setHavingPart(null);

        return $this->addHaving($soql, null, false);
    }

    /**
     * @param LogicalJunction|$soql
     * @return QueryBuilder
     */
    public function havingNot($soql)
    {
        $this->query->setHavingPart(null);

        return $this->addHaving($soql, null, true);
    }

    /**
     * @param string $soql
     * @return QueryBuilder
     */
    public function andHaving($soql)
    {
        return $this->addHaving($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_AND, false);
    }

    /**
     * @param string $soql
     * @return QueryBuilder
     */
    public function andHavingNot($soql)
    {
        return $this->addHaving($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_AND, true);
    }


    /**
     * @param string $soql
     * @return QueryBuilder
     */
    public function orHaving($soql)
    {
        return $this->addHaving($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_OR, false);
    }

    /**
     * @param string $soql
     * @return QueryBuilder
     */
    public function orHavingNot($soql)
    {
        return $this->addHaving($soql, \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_OR, true);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalJunction|string $soql
     * @param null $operator
     * @param bool $isNot
     * @return QueryBuilder
     */
    public function addHaving($soql, $operator = null, $isNot = false)
    {
        if(null === $this->query->getHavingPart())
        {
            $this->query->setHavingPart(
                new \Codemitte\ForceToolkit\Soql\AST\HavingPart(
                    new \Codemitte\ForceToolkit\Soql\AST\LogicalGroup()));
        }
        elseif(null === $operator)
        {
            $operator = \Codemitte\ForceToolkit\Soql\AST\LogicalJunction::OP_AND;
        }

        $junction = new \Codemitte\ForceToolkit\Soql\AST\LogicalJunction();
        $junction->setOperator($operator);
        $junction->setIsNot($isNot);

        if($soql instanceof \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface)
        {
            $junction->setCondition($soql);
        }
        else
        {
            $group = new \Codemitte\ForceToolkit\Soql\AST\LogicalGroup();
            $group->addAll($this->parser->parseHavingSoql($soql));
            $junction->setCondition($group);
        }

        $this->query->getHavingPart()->getLogicalGroup()->add($junction);

        return $this;
    }

    /**
     * @param $soql
     * @param null $dir
     * @param null $nulls
     * @return QueryBuilder
     */
    public function orderBy($soql, $dir = null, $nulls = null)
    {
        $this->query->setOrderPart(new \Codemitte\ForceToolkit\Soql\AST\OrderPart());

        return $this->addOrderBy($soql, $dir, $nulls);
    }

    /**
     * @param $soql
     * @param null $dir
     * @param null $nulls
     * @return QueryBuilder
     */
    public function addOrderBy($soql, $dir = null, $nulls = null)
    {
        if(null === $this->query->getOrderPart())
        {
            return $this->orderBy($soql, $dir, $nulls);
        }

        $orderByFields = $this->parser->parseOrderBySoql($soql);

        // REARRANGE FIELD BY DIR/NULLS
        if(null !== $dir || null !== $nulls)
        {
            foreach($orderByFields AS $orderByField)
            {
                if(null !== $dir)
                {
                    /** @var $orderByField \Codemitte\ForceToolkit\Soql\AST\OrderByField */
                    $orderByField->setDirection(strtoupper($dir));
                }

                if(null !== $nulls)
                {
                    $orderByField->setNulls(strtoupper($nulls));
                }
            }
        }

        $this->query->getOrderPart()->addOrderFields($orderByFields);

        return $this;
    }

    /**
     * @param $limit
     * @return QueryBuilder
     */
    public function limit($limit)
    {
        $this->query->setLimit($limit);

        return $this;
    }

    /**
     * @param $offset
     * @return QueryBuilder
     */
    public function offset($offset)
    {
        $this->query->setOffset($offset);

        return $this;
    }

    /**
     * @param array $parameters
     * @return QueryBuilder
     */
    public function bind(array $parameters)
    {
        $this->mergeParameters($parameters);

        return $this;
    }

    /**
     * @return string
     */
    public function getSoql()
    {
        return $this->renderer->render($this->query, $this->parameters);
    }

    /**
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResult
     */
    public function execute(array $parameters = array())
    {
        $this->bind($parameters);

        $soql = $this->getSoql();

        return $this->client->query($soql);
    }

    /**
     * @param array $parameters
     * @param null $default
     * @return \Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface
     */
    public function getSingleResult(array $parameters = array(), $default = null)
    {
        $res = $this->execute($parameters);

        if(null === $res['result'] || null === $res['result']['records'] || 0 === count($res['result']['records']))
        {
            return $default;
        }
        return $res['result']['records'][0];
    }

    /**
     * Proxy for getSingleResult()
     * @param array $parameters
     * @param null $default
     * @return \Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface
     */
    public function fetchOne(array $parameters = array(), $default = null)
    {
        return $this->getSingleResult($parameters, $default);
    }

    /**
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResultCollection
     */
    public function getResult($parameters = array())
    {
        $res = $this->execute($parameters);

        if(null === $res['result'] || null === $res['result']['records'])
        {
            return new GenericResultCollection();
        }
        return $res['result']['records'];
    }

    /**
     * Proxy for getResult()
     *
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResultCollection
     */
    public function fetch($parameters = array())
    {
        return $this->getResult($parameters);
    }

    /**
     * @param array $parameters
     * @return int
     */
    public function count(array $parameters = array())
    {
        $this->query->setSelectPart(new \Codemitte\ForceToolkit\Soql\AST\SelectPart());

        $this->addSelect('COUNT()');

        $res = $this->execute($parameters);

        if(null === $res['result'] || null === $res['result']['size'])
        {
            return 0;
        }
        return $res['result']['size'];
    }

    /**
     * @param array $parameters
     */
    private function mergeParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Clones the actual query builder
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}