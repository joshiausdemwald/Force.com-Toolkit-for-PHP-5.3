<?php
namespace Codemitte\Sfdc\Soql\AST;

class SelectField extends AbstractSelectable
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
     * @param string $name
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
