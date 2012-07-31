<?php
namespace Codemitte\Sfdc\Soql\AST;

class LogicalGroup extends AbstractSoqlPart implements LogicalConditionInterface
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
    }

    /**
     * @param array $junctions
     */
    public function addAll(array $junctions)
    {
        foreach($junctions AS $junction)
        {
            $this->add($junction);
        }
    }

    /**
     * @return array
     */
    public function getJunctions()
    {
        return $this->junctions;
    }
}
