<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class FromPart extends AbstractCanHazAlias
{
    /**
     * @var string
     */
    private $fromObject;

    /**
     * @param $fromObject
     */
    public function __construct($fromObject)
    {
        $this->fromObject = $fromObject;
    }

    /**
     * @return string
     */
    public function getFromObject()
    {
        return $this->fromObject;
    }
}
