<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlExpression
{
    /**
     * @var string
     */
    private $expression;

    /**
     * @param string $expression
     */
    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function __toString()
    {
        return $this->expression;
    }
}
