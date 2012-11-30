<?php
namespace Codemitte\ForceToolkit\Soql\AST\Functions;

/**
 * Note:
 * If a query includes a GROUP BY or HAVING clause, any currency data returned by using an
 * aggregate function, such as SUM() or MAX(), is in the organization's default currency.
 * You cannot convert the result of an aggregate function into the user's currency by calling
 * theconvertCurrency() function.
 */
class ConvertCurrency extends SoqlFunction
{
    /**
     * @return int: Bitmask calculated out of one or more of
     *              the CONTEXT_* interface constants.
     */
    public function getAllowedContext()
    {
        return self::CONTEXT_SELECT;
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
