<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SelectField extends AbstractCanHazAlias implements SelectFieldInterface
{
    private $name;

    /**
     * Name is the name of the field to select.
     *
     * - Id
     * - Account.Name
     * - COUNT()
     * - COUNT(Account.Name)
     * - toLabel(Account.Picklist)
     *
     * @param SoqlSelectFunction|SoqlAggregateFunction|string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
