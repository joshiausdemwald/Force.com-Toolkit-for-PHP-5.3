<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class HavingField implements HavingFieldInterface
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
     * @param $fieldname
     */
    public function getFieldname($fieldname)
    {
        $this->fieldname = $fieldname;
    }
}
