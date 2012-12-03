<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class TypeofSelectPart implements SelectFieldInterface
{
    /**
     * @var string
     */
    private $sobjectName;

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
     * @param strnig $sobjectName
     */
    public function setSobjectName($sobjectName)
    {
        $this->sobjectName = $sobjectName;
    }

    /**
     * @return string
     */
    public function getSobjectName()
    {
        return $this->sobjectName;
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
