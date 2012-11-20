<?php
namespace Codemitte\ForceToolkit\Soql\AST;

abstract class AbstractSelectable implements SelectableInterface
{
    protected $alias;

    /**
     * @param Alias $alias
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
