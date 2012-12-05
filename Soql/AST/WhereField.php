<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class WhereField implements WhereFieldInterface, ConditionLeftOperandInterface
{
    /**
     * @var string
     */
    private $fieldname;

    /**
     * @param string $fieldname
     */
    public function __construct($fieldname)
    {
        $this->fieldname = $fieldname;
    }

    /**
     * @return string
     */
    public function getFieldname()
    {
        return $this->fieldname;
    }
}
