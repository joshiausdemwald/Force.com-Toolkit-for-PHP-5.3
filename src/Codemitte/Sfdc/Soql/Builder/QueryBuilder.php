<?php
namespace Codemitte\Sfdc\Soql\Builder;

use Codemitte\Sfdc\Soql\Parser\QueryParser;
use Codemitte\Sfdc\Soql\Renderer\QueryRenderer;
use Codemitte\Sfdc\Soql\AST\Query;
use Codemitte\Sfdc\Soap\Client\ClientInterface;

class QueryBuilder
{
    /**
     * @var \Codemitte\Sfdc\Soql\AST\Query
     */
    private $query;

    /**
     * @var \Codemitte\Sfdc\Soql\Parser\QueryParser
     */
    private $parser;

    /**
     * @var \Codemitte\Sfdc\Soql\Renderer\QueryRenderer
     */
    private $renderer;

    /**
     * @var \Codemitte\Sfdc\Soap\Client\ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param \Codemitte\Sfdc\Soap\Client\ClientInterface $client
     * @param \Codemitte\Sfdc\Soql\Parser\QueryParser $parser
     * @param QueryRenderer $renderer
     */
    public function __construct(ClientInterface $client, QueryParser $parser, QueryRenderer $renderer)
    {
        $this->client = $client;

        $this->parser = $parser;

        $this->renderer = $renderer;
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\Query $query
     */
    public function setQuery(Query $query)
    {
        $this->parameters = array();

        $this->query = $query;
    }

    /**
     * @param $soql
     * @param array $parameters
     * @return \Codemitte\Soap\Mapping\GenericResult
     */
    public function query($soql, array $parameters = array())
    {
        $this->setQuery($this->parser->parse($soql));

        return $this->execute($parameters);
    }

    /**
     * @param $soql
     * @return \Codemitte\Sfdc\Soql\Builder\QueryBuilder
     */
    public function select($soql)
    {
        $this->setQuery(new Query());

        $p = new \Codemitte\Sfdc\Soql\AST\SelectPart();

        $this->query->setSelectPart($p);

        return $this->addSelect($soql);
    }

    /**
     * @param $soql
     * @return \Codemitte\Sfdc\Soql\Builder\QueryBuilder
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
     * @param LogicalJunction|string $soql
     * @return QueryBuilder
     */
    public function where($soql)
    {
        $this->query->setWherePart(new \Codemitte\Sfdc\Soql\AST\WherePart(new \Codemitte\Sfdc\Soql\AST\LogicalGroup()));

        return $this->addWhere($soql);
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function andWhere($soql)
    {
        $g = new \Codemitte\Sfdc\Soql\AST\LogicalGroup();

        $g->addAll($this->parser->parseWhereSoql($soql));

        $junction = new \Codemitte\Sfdc\Soql\AST\LogicalJunction();
        $junction->setOperator(\Codemitte\Sfdc\Soql\AST\LogicalJunction::OP_AND);
        $junction->setCondition($g);

        $this->addWhere($junction);

        return $this;
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function orWhere($soql)
    {
        $g = new \Codemitte\Sfdc\Soql\AST\LogicalGroup();

        $g->addAll($this->parser->parseWhereSoql($soql));

        $junction = new \Codemitte\Sfdc\Soql\AST\LogicalJunction();
        $junction->setOperator(\Codemitte\Sfdc\Soql\AST\LogicalJunction::OP_OR);
        $junction->setCondition($g);

        $this->addWhere($junction);

        return $this;
    }

    /**
     * @param LogicalJunction|string $soql
     * @return QueryBuilder
     */
    public function addWhere($soql)
    {
        if(null === $this->query->getWherePart())
        {
            return $this->where($soql);
        }

        if($soql instanceof \Codemitte\Sfdc\Soql\AST\LogicalJunction)
        {
            $this->query->getWherePart()->getLogicalGroup()->add($soql);
        }
        else
        {
            $this->query->getWherePart()->getLogicalGroup()->addAll($this->parser->parseWhereSoql($soql));
        }
        return $this;
    }

    /**
     * @param LogicalJunction|string $soql
     * @return QueryBuilder
     */
    public function withDataCategory($soql)
    {
        $this->query->setWithPart(new \Codemitte\Sfdc\Soql\AST\WithPart(new \Codemitte\Sfdc\Soql\AST\LogicalGroup()));

        return $this->addWithDataCategory($soql);
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function andWithDataCategory($soql)
    {
        $g = new \Codemitte\Sfdc\Soql\AST\LogicalGroup();

        $g->addAll($this->parser->parseWithSoql($soql));

        $junction = new \Codemitte\Sfdc\Soql\AST\LogicalJunction();
        $junction->setOperator(\Codemitte\Sfdc\Soql\AST\LogicalJunction::OP_AND);
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
            return $this->withDataCategory($soql);
        }

        if($soql instanceof \Codemitte\Sfdc\Soql\AST\LogicalJunction)
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
        $this->query->setGroupPart(new \Codemitte\Sfdc\Soql\AST\GroupPart());

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
            $this->query->setGroupPart($g = new \Codemitte\Sfdc\Soql\AST\GroupPart());
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
            $this->query->setGroupPart($g = new \Codemitte\Sfdc\Soql\AST\GroupPart());
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
        $this->query->setHavingPart(new \Codemitte\Sfdc\Soql\AST\HavingPart(new \Codemitte\Sfdc\Soql\AST\LogicalGroup()));

        return $this->addHaving($soql);
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\LogicalJunction|string $soql
     * @return QueryBuilder
     */
    public function addHaving($soql)
    {
        if(null === $this->query->getHavingPart())
        {
            return $this->having($soql);
        }

        if($soql instanceof \Codemitte\Sfdc\Soql\AST\LogicalJunction)
        {
            $this->query->getHavingPart()->getLogicalGroup()->add($soql);
        }
        else
        {
            $this->query->getHavingPart()->getLogicalGroup()->addAll($this->parser->parseHavingSoql($soql));
        }

        return $this;
    }

    /**
     * @param string $soql
     * @return QueryBuilder
     */
    public function andHaving($soql)
    {
        $g = new \Codemitte\Sfdc\Soql\AST\LogicalGroup($soql);

        $g->addAll($this->parser->parseHavingSoql($soql));

        $junction = new \Codemitte\Sfdc\Soql\AST\LogicalJunction();
        $junction->setOperator(\Codemitte\Sfdc\Soql\AST\LogicalJunction::OP_AND);
        $junction->setCondition($g);

        $this->addHaving($junction);

        return $this;
    }

    /**
     * @param string $soql
     * @return QueryBuilder
     */
    public function orHaving($soql)
    {
        $g = new \Codemitte\Sfdc\Soql\AST\LogicalGroup();

        $g->addAll($this->parser->parseHavingSoql($soql));

        $junction = new \Codemitte\Sfdc\Soql\AST\LogicalJunction();
        $junction->setOperator(\Codemitte\Sfdc\Soql\AST\LogicalJunction::OP_OR);
        $junction->setCondition($g);

        $this->addHaving($junction);

        return $this;
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function orderBy($soql)
    {
        $this->query->setOrderPart(new \Codemitte\Sfdc\Soql\AST\OrderPart());

        return $this->addOrderBy($soql);
    }

    /**
     * @param $soql
     * @return QueryBuilder
     */
    public function addOrderBy($soql)
    {
        if(null === $this->query->getOrderPart())
        {
            return $this->orderBy($soql);
        }

        $this->query->getOrderPart()->addOrderFields($this->parser->parseOrderBySoql($soql));

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
     * @return \Codemitte\Sfdc\Soql\AST\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param array $parameters
     * @return QueryBuilder
     */
    public function bind(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return string
     */
    public function getSoql()
    {
        return $this->renderer->render($this->getQuery(), $this->parameters);
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
}
