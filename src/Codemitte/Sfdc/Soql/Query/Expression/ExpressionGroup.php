<?php
namespace Codemitte\Sfdc\Soql\Query\Expression;

class ExpressionGroup implements ExpressionInterface
{
    private $parts;

    private $name;

    /**
     * @param $name
     * @param array $parts
     */
    public function __construct($name, array $parts)
    {
        $this->addAll($parts);
    }

    public function addAll(array $parts)
    {
        foreach($parts AS $part)
        {
            if( ! $part instanceof ExpressionInterface)
            {
                throw new \InvalidArgumentException(sprintf('Parts "%s" must be instance of ExpressionInterface.', $part->getName()));
            }
            $this->add($part);
        }
    }

    /**
     * @param $part
     */
    public function add($part)
    {
        $this->parts[] = $part;
    }

    public function getName()
    {
        return $this->getName();
    }
}
