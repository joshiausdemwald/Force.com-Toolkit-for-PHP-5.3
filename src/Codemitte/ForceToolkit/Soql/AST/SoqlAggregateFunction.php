<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlAggregateFunction implements SoqlExpressionInterface, GroupableInterface
{
    /**
     * @var string
     */
    protected $argument;

    /**
     * @var String
     */
    protected $name;

    /**
     * @param $name
     * @param string $argument
     */
    public function __construct($name, $argument)
    {
        $this->name = $name;

        $this->argument = $argument;
    }

    /**
     * @return SoqlExpression
     */
    public function getArgument()
    {
        return $this->argument;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getExpression()
    {
        return $this->getName();
    }

    public function __toString()
    {
        return $this->getName() . '(' . ($this->getArgument() ? $this->getArgument() : '') . ')';
    }
}
