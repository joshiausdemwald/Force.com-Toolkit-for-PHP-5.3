<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlFunction implements SoqlExpressionInterface, SoqlFunctionArgumentInterface
{
    protected $arguments;

    protected $name;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->arguments = array();
    }

    /**
     * @param SoqlFunctionArgumentInterface $argument
     */
    public function addArgument(SoqlFunctionArgumentInterface $argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * @return array<SoqlFunctionArgumentInterface>
     */
    public function getArguments()
    {
        return $this->arguments;
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
        $args = array();

        foreach($this->arguments AS $arg)
        {
            $args[] = (string)$arg;
        }

        return $this->getName() . '(' . implode(', ', $arg) . ')';
    }
}
