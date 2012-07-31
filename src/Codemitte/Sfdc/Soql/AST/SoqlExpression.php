<?php
namespace Codemitte\Sfdc\Soql\AST;

class SoqlExpression implements SoqlExpressionInterface, SoqlFunctionArgumentInterface
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

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }
}
