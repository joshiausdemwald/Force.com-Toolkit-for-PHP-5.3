<?php
namespace Codemitte\ForceToolkit\Soql\AST\Functions;

class Count extends SoqlFunction
{
    protected $name = 'COUNT';

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
        return array();
    }
}
