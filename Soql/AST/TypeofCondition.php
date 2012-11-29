<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class TypeofCondition
{
    /**
     * @var string
     */
    private $sobjectType;

    /**
     * @var SelectPart
     */
    private $selectPart;

    /**
     * @param string $sobjectType
     * @param SelectPart $selectPart
     */
    public function __construct($sobjectType, SelectPart $selectPart)
    {
        $this->sobjectType = $sobjectType;

        $this->selectPart = $selectPart;
    }

    /**
     * @return SelectPart
     */
    public function getSelectPart()
    {
        return $this->selectPart;
    }

    /**
     * @return string
     */
    public function getSobjectType()
    {
        return $this->sobjectType;
    }
}
