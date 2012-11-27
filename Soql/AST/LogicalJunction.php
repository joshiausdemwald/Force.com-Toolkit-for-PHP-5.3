<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class LogicalJunction
{
    const
        OP_AND = 'AND',
        OP_OR  = 'OR';

    /**
     * @var boolean
     */
    private $isNot;

    /**
     * "AND", "OR"
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $condition;

    /**
     * @param boolean|null $isNot
     * @param string $operator: "AND"/"OR", on the of OP_* constants
     * @param LogicalConditionInterface $condition: Can be logical condition or another logical (sub-) group
     */
    public function __construct($isNot = null, $operator = null, LogicalConditionInterface $condition = null)
    {
        $this->isNot = $isNot;

        $this->operator = $operator;

        $this->condition = $condition;
    }

    /**
     * @param bool $isNot
     */
    public function setIsNot($isNot = true)
    {
        $this->isNot = $isNot;
    }

    /**
     * @param $operator: One of the OP_* constants, "AND"/"OR"
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @param LogicalConditionInterface $condition
     */
    public function setCondition(LogicalConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return boolean
     */
    public function getIsNot()
    {
        return $this->isNot;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
}
