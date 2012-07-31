<?php
namespace Codemitte\Sfdc\Soql\AST;

class SoqlFunction implements SoqlExpressionInterface, SoqlFunctionArgumentInterface
{
    private $arguments;

    private $name;

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
}
