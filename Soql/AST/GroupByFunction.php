<?php
namespace Codemitte\ForceToolkit\Soql\AST;

use Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface;

class GroupByFunction implements GroupableInterface
{
    /**
     * @var Functions\SoqlFunctionInterface
     */
    private $function;

    /**
     * @param Functions\SoqlFunctionInterface $function
     */
    public function __construct(SoqlFunctionInterface $function)
    {
        $this->function = $function;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface
     */
    public function getFunction()
    {
        return $this->function;
    }
}
