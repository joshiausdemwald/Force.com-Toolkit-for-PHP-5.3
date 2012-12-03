<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SelectPart
{
    /**
     * @var array<SelectFieldInterface>
     */
    private $selectFields;

    /**
     * @param array $selectFields
     */
    public function __construct(array $selectFields = array())
    {
        $this->addSelectFields($selectFields);
    }

    /**
     * @param SelectFieldInterface $field
     * @return void
     */
    public function addSelectField($field)
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
     * @return array<SelectFieldInterface>
     */
    public function getSelectFields()
    {
        return $this->selectFields;
    }
}
