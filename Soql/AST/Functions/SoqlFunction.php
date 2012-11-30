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

        $this->validateArguments();
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
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

    private function validateArguments()
    {
        $arguments = $this->getArguments();

        $currentCount = count($arguments);

        $requiredCount = $this->getNumArguments();

        if($currentCount < $requiredCount)
        {
            throw new \InvalidArgumentException(sprintf('Missing argument(s) for function "%s": %d provided, %d required', $this->getName(), $currentCount, $requiredCount));
        }
        elseif ($currentCount > $requiredCount)
        {
            throw new \InvalidArgumentException(sprintf('Too many arguments provided for function "%s": %d required, %d given.', $this->getName(), $requiredCount, $currentCount));
        }

        $allowedArguments = $this->getAllowedArguments();

        foreach($arguments AS $i => $argument)
        {
            $definition = $allowedArguments[$i];

            if( ! is_array($definition))
            {
                $definition = array($definition);
            }

            $clazz = get_class($argument);

            if( ! in_array($clazz, $definition))
            {
                throw new \InvalidArgumentException(sprintf('Argument %d must be one of type "%s", "%s" given.', $i, implode('", "', $definition), $clazz));
            }
        }
    }
}
