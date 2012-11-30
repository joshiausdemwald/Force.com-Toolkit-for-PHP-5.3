<?php
namespace Codemitte\ForceToolkit\Soql\AST\Functions\Aggregate;

use Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunction;

abstract class AggregateFunction extends SoqlFunction implements AggregateFunctionInterface
{
    public function getAllowedContext()
    {
        return self::CONTEXT_SELECT | self::CONTEXT_HAVING | self::CONTEXT_GROUP_BY | self::CONTEXT_ORDER_BY;
    }

    /**
     * Returns an array (one entry for each argument) containing
     * a list of class names that are allowed as an argument
     * for the particular function.
     *
     * @return array<array<string>>
     */
    public function getAllowedArguments()
    {
        return array(
            array('Codemitte\ForceToolkit\Soql\AST\SoqlName')
        );
    }
}
