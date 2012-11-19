<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class WithPart
{
    const WITH_DATA_CATEGORY = 'DATA CATEGORY';

    /**
     * @var string
     */
    private $with;

    /**
     * @var LogicalGroup
     */
    private $logicalGroup;

    /**
     * @param LogicalGroup $logicalGroup
     * @param string $with
     */
    public function __construct(LogicalGroup $logicalGroup, $with = self::WITH_DATA_CATEGORY)
    {
        $this->logicalGroup= $logicalGroup;

        $this->with = $with;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\LogicalGroup
     */
    public function getLogicalGroup()
    {
        return $this->logicalGroup;
    }

    /**
     * @return string
     */
    public function getWith()
    {
        return $this->with;
    }
}
