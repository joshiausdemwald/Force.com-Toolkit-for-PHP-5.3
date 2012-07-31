<?php
namespace Codemitte\Sfdc\Soql\AST;

class FromPart extends AbstractSoqlPart
{
    /**
     * @var string
     */
    private $fromObject;

    /**
     * @var string
     */
    private $alias;

    /**
     * @param $fromObject
     */
    public function __construct($fromObject)
    {
        $this->fromObject = $fromObject;
    }

    /**
     * @param Alias $alias
     * @return void
     */
    public function setAlias(Alias $alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getFromObject()
    {
        return $this->fromObject;
    }
}
