<?php
namespace Codemitte\Sfdc\Soql\AST;

class SelectPart extends AbstractSoqlPart
{
    /**
     * @var array<SelectableInterface>
     */
    private $selectFields;

    public function __construct()
    {
        $this->selectFields = array();
    }

    /**
     * @param SelectableInterface $field
     * @internal param $ \Codemitte\Sfdc\Soql\AST\Selectable
     * @return void
     */
    public function addSelectField(SelectableInterface $field)
    {
        $this->selectFields[] = $field;
    }

    /**
     * @param array $selectFields
     */
    public function addSelectFields(array $selectFields)
    {
        foreach($selectFields AS $selectField)
        {
            $this->selectFields[] = $selectField;
        }
    }

    /**
     * @return array<SelectableInterface>
     */
    public function getSelectFields()
    {
        return $this->selectFields;
    }
}
