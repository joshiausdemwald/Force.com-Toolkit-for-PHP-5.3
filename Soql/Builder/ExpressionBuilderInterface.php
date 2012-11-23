<?php
namespace Codemitte\ForceToolkit\Soql\Builder;

/**
 * ExpressionBuilderInterface
 * Fluent interface for building boolean
 * expressions.
 *
 * Usage example:
 *
 * $right: name
 * $op: Operator (one of the OP_* constancts)
 * $left: Scalar Value, collection or query
 *
 * $builder
 *     ->xpr($right, $op, $left)
 *     ->andXpr(
 *         $builder
 *             ->xpr($right, $op, $left)
 *             ->orXpr($right, $op, $left)
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

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function xpr($right, $op = null, $left = null);

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function notXpr($right, $op = null, $left = null);

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function andXpr($right, $op = null, $left = null);

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function andNotXpr($right, $op = null, $left = null);

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function orXpr($right, $op = null, $left = null);

    /**
     * @param $right
     * @param null $op
     * @param null $left
     * @return ExpressionBuilderInterface
     */
    public function orNotXpr($right, $op = null, $left = null);
}

