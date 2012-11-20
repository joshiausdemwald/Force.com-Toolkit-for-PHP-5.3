<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class WherePart
{
    /**
     * @var LogicalGroup
     */
    private $logicalGroup;

    /**
     * @param LogicalGroup $logicalGroup
     */
    public function __construct(LogicalGroup $logicalGroup = null)
    {
        $this->setLogicalGroup($logicalGroup);
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\LogicalGroup
     */
    public function getLogicalGroup()
    {
        return $this->logicalGroup;
    }

    /**
     * @param LogicalGroup $logicalGroup
     */
    public function setLogicalGroup(LogicalGroup $logicalGroup)
    {
        $this->logicalGroup = $logicalGroup;
    }
}
