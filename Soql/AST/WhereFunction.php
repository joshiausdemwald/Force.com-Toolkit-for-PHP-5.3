<?php
namespace Codemitte\ForceToolkit\Soql\AST;

use Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface;

class WhereFunction implements WhereFieldInterface, ConditionLeftOperandFunctionInterface
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
     * @return Functions\SoqlFunctionInterface
     */
    public function getFunction()
    {
        return $this->function;
    }

    public function getFieldname()
    {
        return $this->getFunction()->getName();
    }
}
