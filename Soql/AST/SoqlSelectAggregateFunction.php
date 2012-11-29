<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlSelectAggregateFunction extends SoqlAggregateFunction implements SelectableFieldInterface
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
