<?php
namespace Codemitte\ForceToolkit\Soql\AST\Functions;

abstract class SoqlFunction implements SoqlFunctionInterface
{
    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments = array())
    {
        $this->setArguments($arguments);
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the total (mandatory) number of arguments.
     *
     * @return int
     */
    public function getNumArguments()
    {
        return count($this->getAllowedArguments());
    }
}
