<?php
namespace Codemitte\ForceToolkit\Soql\AST;

interface CanHazAliasInterface
{
    /**
     * @param Alias $alias
     * @return void
     */
    public function setAlias(Alias $alias);

    /**
     * @return Alias
     */
    public function getAlias();

    /**
     * @return bool
     */
    public function hasAlias();
}
