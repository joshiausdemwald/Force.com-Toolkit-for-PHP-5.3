<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class LogicalCondition
{
    /**
     * @var SoqlName|\Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface
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
     * @param SoqlName|\Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface $left
     * @param string|null $operator
     * @param mixed $right
     */
    public function __construct($left = null, $operator = null, $right = null)
    {
        $this->left = $left;

        $this->operator = $operator;

        $this->right = $right;
    }

    /**
     * @param SoqlName|\Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface $left
     */
    public function setLeft($left)
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
     * @param mixed $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * @return SoqlName|\Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface
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
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }
}
