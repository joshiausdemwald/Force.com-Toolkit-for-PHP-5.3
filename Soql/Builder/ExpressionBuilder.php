<?php
namespace Codemitte\ForceToolkit\Soql\Builder;

use Codemitte\ForceToolkit\Soql\AST;


class ExpressionBuilder implements ExpressionBuilderInterface
{
    private $expression;

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function xpr($right, $op = null, $left = null)
    {
        return $this->buildExpression($right, $op, $left);
    }

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function notXpr($right, $op = null, $left = null)
    {
        return $this->buildExpression($right, $op, $left, null, true);
    }

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function andXpr($right, $op = null, $left = null)
    {
        return $this->buildExpression($right, $op, $left, 'AND');
    }

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function andNotXpr($right, $op = null, $left = null)
    {
        return $this->buildExpression($right, $op, $left, 'AND', true);
    }

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function orXpr($right, $op = null, $left = null)
    {
        return $this->buildExpression($right, $op, $left, 'OR');
    }

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function orNotXpr($right, $op = null, $left = null)
    {
        return $this->buildExpression($right, $op, $left, 'OR', true);
    }

    /**
     * @param $right
     * @param int $op
     * @param mixed $left: string|collection|subquery
     * @param string|null $junction; NULL|"AND"|"OR"
     * @param bool $not
     * @return ExpressionBuilderInterface
     */
    private function buildExpression($right, $op = null, $left = null, $junction = null, $not = false)
    {
        $expression = $this->getExpression();

        // NESTED EXPRESSION
        if($right instanceof ExpressionBuilderInterface)
        {
            $expression->add(new AST\LogicalJunction($not, $junction, $right->getExpression()));
        }

        // NAME
        else
        {
            $expression->add(new AST\LogicalJunction($not, $junction, new AST\LogicalCondition($right, $op, $left)));
        }

        return $this;
    }

    /**
     * @return AST\LogicalGroup
     */
    private function getExpression()
    {
        if(null === $this->expression)
        {
            $this->expression = new AST\LogicalGroup();
        }
        return $this->expression;
    }
}
