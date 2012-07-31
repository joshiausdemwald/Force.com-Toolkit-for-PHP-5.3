<?php
namespace Codemitte\Sfdc\Soql\AST;

interface SelectableInterface
{
    /**
     * @abstract
     * @param Alias $alias
     * @return mixed
     */
    public function setAlias(Alias $alias);
}
