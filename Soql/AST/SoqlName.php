<?php
namespace Codemitte\ForceToolkit\Soql\AST;

/**
 * Arbitrary SOQL identifier, e.g. Sobject or Fieldname:
 *
 * "Account"
 * "CustomObject__c"
 * "CustomObject__c.fieldName__r.fieldName"
 */
class SoqlName
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
