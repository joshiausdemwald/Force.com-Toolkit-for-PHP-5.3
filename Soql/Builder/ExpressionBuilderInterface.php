<?php
namespace Codemitte\ForceToolkit\Soql\Builder;

/**
 * ExpressionBuilderInterface
 * Fluent interface for building boolean
 * expressions.
 *
 * Usage example:
 *
 * $left: name
 * $op: Operator (one of the OP_* constancts)
 * $right: Scalar Value, collection or query
 *
 * $builder
 *     ->xpr($left, $op, $right)
 *     ->andXpr(
 *         $builder
 *             ->xpr($left, $op, $right)
 *             ->orXpr($left, $op, $right)
 *     )
 *     ->andNotXpr()
 *
 * @interface
 * @abstract
 */
interface ExpressionBuilderInterface
{
    const
        OP_GT = '>',
        OP_LT = '<',
        OP_LTE = '<=',
        OP_GTE = '>=',
        OP_EQ = '=',
        OP_NEQ = '!=',
        OP_LIKE = 'LIKE',
        OP_INCLUDES = 'INCLUDES',
        OP_EXCLUDES = 'EXCLUDES',
        OP_IN = 'IN',
        OP_NOT_IN = 'NOT IN'
    ;

    const
        CONTEXT_WHERE = 1,
        CONTEXT_HAVING = 2;


    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function xpr($left, $op = null, $right = null);

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function notXpr($left, $op = null, $right = null);

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function andXpr($left, $op = null, $right = null);

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function andNotXpr($left, $op = null, $right = null);

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function orXpr($left, $op = null, $right = null);

    /**
     * @param string $left: Name, function() or AggregateFunction()
     * @param null $op
     * @param null $right
     * @return ExpressionBuilderInterface
     */
    public function orNotXpr($left, $op = null, $right = null);

    /**
     * @return int $context: One of the CONTEXT_* constants
     */
    public function getContext();

    /**
     * @param int $context: One of the CONTEXT_* constants.
     */
    public function setContext($context);

    /**
     * Returns the built-up expression to inject in in any
     * arbitrary query AST:
     *
     * @return \Codemitte\ForceToolkit\Soql\AST\LogicalGroup
     */
    public function getExpression();
}

