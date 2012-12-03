<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class TypeofCondition
{
    /**
     * @var string
     */
    private $sobjectFieldname;

    /**
     * @var SelectPart
     */
    private $selectPart;

    /**
     * @param string $sobjectFieldname
     * @param SelectPart $selectPart
     */
    public function __construct($sobjectFieldname, SelectPart $selectPart)
    {
        $this->sobjectFieldname = $sobjectFieldname;

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
    public function getSobjectFieldname()
    {
        return $this->sobjectFieldname;
    }
}
