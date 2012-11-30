<?php
namespace Codemitte\ForceToolkit\Soql\AST\Functions;

/**
 * SoqlFunctionInterface
 *
 * Interface for defining arbitrary (SOQL) functions.
 * Aggregate functions are a "specialized" version of
 * a soql function and are stored underneath the
 * "Aggregate" sub-namespace.
 *
 * @Interface
 * @Abstract
 */
interface SoqlFunctionInterface
{
    const
        CONTEXT_SELECT = 1,
        CONTEXT_WHERE = 2,
        CONTEXT_HAVING = 4,
        CONTEXT_GROUP_BY = 8,
        CONTEXT_ORDER_BY = 16,
        CONTEXT_WITH_DATA_CATEGORY = 32
    ;

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments);

    /**
     * @return mixed
     */
    public function getArguments();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int: Bitmask calculated out of one or more of
     *              the CONTEXT_* interface constants.
     */
    public function getAllowedContext();

    /**
     * Returns an array (one entry for each argument) containing
     * a list of class names that are allowed as an argument
     * for the particular function.
     *
     * @return array<array<string>>
     */
    public function getAllowedArguments();

    /**
     * Returns the total (mandatory) number of arguments.
     *
     * @return int
     */
    public function getNumArguments();
}
