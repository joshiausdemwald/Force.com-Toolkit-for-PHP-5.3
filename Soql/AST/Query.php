<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class Query implements QueryInterface
{
    /**
     * @var SelectPart $selectPart;
     */
    private $selectPart;

    /**
     * @var FromPart $fromPart
     */
    private $fromPart;

    /**
     * @var WherePart $wherePart
     */
    private $wherePart;

    /**
     * @var WithPart $withPart
     */
    private $withPart;

    /**
     * @var GroupByExpression $groupPart
     */
    private $groupPart;

    /**
     * @var HavingPart $havingPart
     */
    private $havingPart;

    /**
     * @var OrderPart $orderPart
     */
    private $orderPart;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @param SelectPart $selectPart
     */
    public function setSelectPart(SelectPart $selectPart = null)
    {
        $this->selectPart = $selectPart;
    }

    /**
     * @param FromPart $fromPart
     * @internal param \Codemitte\ForceToolkit\Soql\AST\SelectPart $selectPart
     */
    public function setFromPart(FromPart $fromPart = null)
    {
        $this->fromPart = $fromPart;
    }

    /**
     * @param WherePart $wherePart
     */
    public function setWherePart(WherePart $wherePart = null)
    {
        $this->wherePart = $wherePart;
    }

    /**
     * @param Withpart $withPart
     */
    public function setWithPart(WithPart $withPart = null)
    {
        $this->withPart = $withPart;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\GroupByExpression $groupPart
     */
    public function setGroupPart(GroupByExpression $groupPart = null)
    {
        $this->groupPart = $groupPart;
    }

    /**
     * @param HavingPart $havingPart
     */
    public function setHavingPart(HavingPart $havingPart = null)
    {
        $this->havingPart = $havingPart;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\OrderPart $orderPart
     */
    public function setOrderPart(OrderPart $orderPart = null)
    {
        $this->orderPart = $orderPart;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit = null)
    {
        $this->limit = $limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset = null)
    {
        $this->offset = $offset;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\FromPart
     */
    public function getFromPart()
    {
        return $this->fromPart;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\GroupPart
     */
    public function getGroupPart()
    {
        return $this->groupPart;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\HavingPart
     */
    public function getHavingPart()
    {
        return $this->havingPart;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\OrderPart
     */
    public function getOrderPart()
    {
        return $this->orderPart;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\SelectPart
     */
    public function getSelectPart()
    {
        return $this->selectPart;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\WherePart
     */
    public function getWherePart()
    {
        return $this->wherePart;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\WithPart
     */
    public function getWithPart()
    {
        return $this->withPart;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this;
    }
}
