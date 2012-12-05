<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class Subquery extends AbstractCanHazAlias implements QueryInterface, SelectFieldInterface, ComparableInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    public function __toString()
    {
        return '';
    }
}
