<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class LogicalGroup
{
    /**
     * @var array
     */
    private $junctions;

    public function __construct()
    {
        $this->junctions= array();
    }

    /**
     * @param LogicalJunction $junction
     * @return void
     */
    public function add(LogicalJunction $junction)
    {
        if(count($this->junctions) > 0 && null === $junction->getOperator())
        {
            $junction->setOperator(LogicalJunction::OP_AND);
        }
        $this->junctions[] = $junction;

        return $this;
    }

    /**
     * @param array $junctions
     * @return \Codemitte\ForceToolkit\Soql\AST\LogicalGroup
     */
    public function addAll(array $junctions)
    {
        foreach($junctions AS $junction)
        {
            $this->add($junction);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getJunctions()
    {
        return $this->junctions;
    }
}
