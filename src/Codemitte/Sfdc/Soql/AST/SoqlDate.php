<?php
namespace Codemitte\Sfdc\Soql\AST;

class SoqlDate extends SoqlValue
{
    /**
     * @param \DateTime $date
     */
    public function __construct(\DateTime $date)
    {
        $this->value = $date;
    }
}
