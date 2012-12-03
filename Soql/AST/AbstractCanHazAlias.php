<?php
namespace Codemitte\ForceToolkit\Soql\AST;

abstract class AbstractCanHazAlias implements CanHazAliasInterface
{
    protected $alias;

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
     * @return bool
     */
    public function hasAlias()
    {
        return null !== $this->alias;
    }
}
