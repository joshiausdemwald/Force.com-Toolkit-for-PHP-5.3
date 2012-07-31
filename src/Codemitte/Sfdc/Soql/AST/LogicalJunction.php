<?php
namespace Codemitte\Sfdc\Soql\AST;

class LogicalJunction extends AbstractSoqlPart
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
     * @param bool $isNot
     */
    public function setIsNot($isNot = true)
    {
        $this->isNot = $isNot;
    }

    /**
     * @param $operator
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
