<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class TypeofSelectPart implements SelectableInterface
{
    /**
     * @var string
     */
    private $sobjectType;

    /**
     * @var array<TypeofCondition>
     */
    private $conditions;

    /**
     * @var SelectPart
     */
    private $else;

    public function __construct()
    {
        $this->conditions = array();
    }

    /**
     * @param strnig $sobjectType
     */
    public function setSobjectType($sobjectType)
    {
        $this->sobjectType = $sobjectType;
    }

    /**
     * @return string
     */
    public function getSobjectType()
    {
        return $this->sobjectType;
    }

    /**
     * @param TypeofCondition $condition
     */
    public function addCondition(TypeofCondition $condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * @param SelectPart $else
     */
    public function setElse(SelectPart $else)
    {
        $this->else = $else;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function getElse()
    {
        return $this->else;
    }
}
