<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class LogicalCondition implements LogicalConditionInterface
{
    /**
     * @var SoqlExpressionInterface
     */
    private $left;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var ComparableInterface
     */
    private $right;

    /**
     * @param SoqlExpressionInterface $left
     */
    public function setLeft(SoqlExpressionInterface $left)
    {
        $this->left = $left;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @param ComparableInterface $right
     */
    public function setRight(ComparableInterface $right)
    {
        $this->right = $right;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlExpression
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\ComparableInterface
     */
    public function getRight()
    {
        return $this->right;
    }
}
