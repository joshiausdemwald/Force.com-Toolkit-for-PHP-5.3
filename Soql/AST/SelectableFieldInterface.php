<?php
namespace Codemitte\ForceToolkit\Soql\AST;

interface SelectableFieldInterface extends SelectableInterface
{
    /**
     * @abstract
     * @param Alias $alias
     * @return Alias
     */
    public function setAlias(Alias $alias);

    /**
     * @abstract
     * @return Alias
     */
    public function getAlias();

    /**
     * @abstract
     * @return boolean
     */
    public function hasAlias();
}
