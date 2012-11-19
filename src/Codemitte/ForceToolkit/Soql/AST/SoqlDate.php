<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlDate extends SoqlValue
{
    /**
     * @param \DateTime $date
     */
    public function __construct(\DateTime $date)
    {
        $this->value = $date;
    }

    public function __toString()
    {
        return $this->value->format('Y-m-d');
    }
}
