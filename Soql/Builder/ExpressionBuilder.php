<?php
namespace Codemitte\ForceToolkit\Soql\Builder;

use
    Codemitte\ForceToolkit\Soql\AST,
    Codemitte\ForceToolkit\Soql\Parser\QueryParser
;


class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var \Codemitte\ForceToolkit\Soql\AST\LogicalGroup
     */
    private $expression;

    /**
     * @var \Codemitte\ForceToolkit\Soql\Parser\QueryParser
     */
    private $parser;

    /**
     * @var string
     */
    private $context;

    /**
     * @param \Codemitte\ForceToolkit\Soql\Parser\QueryParser $parser
     * @param int $context: One of the CONTEXT_* constants (CONTEXT_WHERE, CONTEXT_HAVING)
     */
    public function __construct(QueryParser $parser, $context = self::CONTEXT_WHERE)
    {
        $this->parser = $parser;

        $this->setContext($context);
    }

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function xpr($left, $op = null, $right = null)
    {
        return $this->buildExpression($left, $op, $right);
    }

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function notXpr($left, $op = null, $right = null)
    {
        return $this->buildExpression($left, $op, $right, null, true);
    }

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function andXpr($left, $op = null, $right = null)
    {
        return $this->buildExpression($left, $op, $right, 'AND');
    }

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function andNotXpr($left, $op = null, $right = null)
    {
        return $this->buildExpression($left, $op, $right, 'AND', true);
    }

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function orXpr($left, $op = null, $right = null)
    {
        return $this->buildExpression($left, $op, $right, 'OR');
    }

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function orNotXpr($left, $op = null, $right = null)
    {
        return $this->buildExpression($left, $op, $right, 'OR', true);
    }

    /**
     * @return int $context: One of the CONTEXT_* constants
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param int $context: One of the CONTEXT_* constants.
     * @return void
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return AST\LogicalGroup
     */
    public function getExpression()
    {
        if(null === $this->expression)
        {
            $this->expression = new AST\LogicalGroup();
        }
        return $this->expression;
    }

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param int $op
     * @param mixed $right: string|collection|subquery
     * @param string|null $junction; NULL|"AND"|"OR"
     * @param bool $not
     * @return ExpressionBuilderInterface
     */
    private function buildExpression($left, $op = null, $right = null, $junction = null, $not = false)
    {
        $expression = $this->getExpression();

        // NESTED EXPRESSION
        if($left instanceof ExpressionBuilderInterface)
        {
            $expression->add(new AST\LogicalJunction($not, $junction, $left->getExpression()));
        }

        // NAME
        else
        {
            $expression->add(new AST\LogicalJunction($not, $junction, new AST\LogicalCondition($this->buildLeftExpression($left), $op, $this->buildRightExpression($right))));
        }

        return $this;
    }

    /**
     * Returns an instance of SoqlExpression interface, dependent on
     * the given string, an plain Expression (fieldname), Function() or
     * AggregateFunction()
     *
     * @param string $right
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlExpressionInterface
     */
    private function buildRightExpression($right)
    {
        // BASIC TYPE CHECK. POSSIBLE VALUES:
        // array() [collection value]
        // string: INLINE QUERY, OR FORMULA, OR VARIABLE
        if(self::CONTEXT_HAVING === $this->getContext())
        {
            return $this->parser->parseRightHavingSoql($right);
        }
        return $this->parser->parseRightWhereSoql($right);
    }

    /**
     * Returns an instance of SoqlExpression interface, dependent on
     * the given string, an plain Expression (fieldname), Function() or
     * AggregateFunction()
     *
     * @param $left
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlExpressionInterface
     */
    private function buildLeftExpression($left)
    {
        if(self::CONTEXT_HAVING === $this->getContext())
        {
            return $this->parser->parseLeftHavingSoql($left);
        }
        return $this->parser->parseLeftWhereSoql($left);
    }
}
