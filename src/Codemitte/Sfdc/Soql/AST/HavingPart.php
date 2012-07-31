<?php
namespace Codemitte\Sfdc\Soql\AST;

class HavingPart extends AbstractSoqlPart
{
    /**
     * @var LogicalGroup
     */
    private $logicalGroup;

    /**
     * @param LogicalGroup $logicalGroup
     */
    public function __construct(LogicalGroup $logicalGroup)
    {
        $this->logicalGroup= $logicalGroup;
    }

    /**
     * @return \Codemitte\Sfdc\Soql\AST\LogicalGroup
     */
    public function getLogicalGroup()
    {
        return $this->logicalGroup;
    }
}
