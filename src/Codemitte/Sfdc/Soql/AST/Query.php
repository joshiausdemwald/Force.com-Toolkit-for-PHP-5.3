<?php
namespace Codemitte\Sfdc\Soql\AST;

class Query
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
     * @var GroupPart $groupPart
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
    public function setSelectPart(SelectPart $selectPart)
    {
        $this->selectPart = $selectPart;
    }

    /**
     * @param FromPart $fromPart
     * @internal param \Codemitte\Sfdc\Soql\AST\SelectPart $selectPart
     */
    public function setFromPart(FromPart $fromPart)
    {
        $this->fromPart = $fromPart;
    }

    /**
     * @param WherePart $wherePart
     */
    public function setWherePart(WherePart $wherePart)
    {
        $this->wherePart = $wherePart;
    }

    /**
     * @param Withpart $withPart
     */
    public function setWithPart(WithPart $withPart)
    {
        $this->withPart = $withPart;
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\GroupPart $groupPart
     */
    public function setGroupPart(GroupPart $groupPart)
    {
        $this->groupPart = $groupPart;
    }

    /**
     * @param HavingPart $havingPart
     */
    public function setHavingPart(HavingPart $havingPart)
    {
        $this->havingPart = $havingPart;
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\OrderPart $orderPart
     */
    public function setOrderPart(OrderPart $orderPart)
    {
        $this->orderPart = $orderPart;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return \Codemitte\Sfdc\Soql\AST\FromPart
     */
    public function getFromPart()
    {
        return $this->fromPart;
    }

    /**
     * @return \Codemitte\Sfdc\Soql\AST\GroupPart
     */
    public function getGroupPart()
    {
        return $this->groupPart;
    }

    /**
     * @return \Codemitte\Sfdc\Soql\AST\HavingPart
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
     * @return \Codemitte\Sfdc\Soql\AST\OrderPart
     */
    public function getOrderPart()
    {
        return $this->orderPart;
    }

    /**
     * @return \Codemitte\Sfdc\Soql\AST\SelectPart
     */
    public function getSelectPart()
    {
        return $this->selectPart;
    }

    /**
     * @return \Codemitte\Sfdc\Soql\AST\WherePart
     */
    public function getWherePart()
    {
        return $this->wherePart;
    }

    /**
     * @return \Codemitte\Sfdc\Soql\AST\WithPart
     */
    public function getWithPart()
    {
        return $this->withPart;
    }
}
