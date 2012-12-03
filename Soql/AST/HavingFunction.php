<?php
namespace Codemitte\ForceToolkit\Soql\AST;

use Codemitte\ForceToolkit\Soql\AST\Functions\Aggregate\AggregateFunctionInterface;

class HavingFunction
{
    /**
     * @var Functions\Aggregate\AggregateFunctionInterface
     */
    private $function;

    /**
     * @param Functions\Aggregate\AggregateFunctionInterface $function
     */
    public function __construct(AggregateFunctionInterface $function)
    {
        $this->function = $function;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\Functions\Aggregate\AggregateFunctionInterface
     */
    public function getFunction()
    {
        return $this->function;
    }
}
