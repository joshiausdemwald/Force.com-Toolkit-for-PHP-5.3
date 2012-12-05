<?php
namespace Codemitte\ForceToolkit\Soql\Renderer;

use
    Codemitte\ForceToolkit\Soql\AST AS AST,
    Codemitte\ForceToolkit\Soql\AST\Functions AS Functions,
    Codemitte\ForceToolkit\Soql\Type\TypeFactory;


class QueryRenderer
{
    /**
     * @var \Codemitte\ForceToolkit\Soql\Type\TypeFactory
     */
    private $typeFactory;

    /**
     * @var Integer
     */
    private $varIndex;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param \Codemitte\ForceToolkit\Soql\Type\TypeFactory $typeFactory
     */
    public function __construct(TypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\Query $query
     * @param array $parameters
     * @return string
     */
    public function render(AST\Query $query, array $parameters = array())
    {
        $this->varIndex = 0;

        $this->parameters = $parameters;

        return $this->renderQuery($query);
    }

    /**
     * QueryInterface:
     * May pass query or subquery to render (Subqueries are aliasable.)
     *
     * @param \Codemitte\ForceToolkit\Soql\AST\QueryInterface $query
     * @return string
     */
    private function renderQuery(AST\QueryInterface $query)
    {
        $query = $query->getQuery();
        return
            $this->renderSelect($query->getSelectPart()) .
            $this->renderFrom($query->getFromPart()) .
            $this->renderWhere($query->getWherePart()) .
            $this->renderWith($query->getWithPart()) .
            $this->renderGroup($query->getGroupPart()) .
            $this->renderHaving($query->getHavingPart()) .
            $this->renderOrder($query->getOrderPart()) .
            $this->renderLimit($query->getLimit()) .
            $this->renderOffset($query->getOffset())
        ;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\SelectPart $selectPart
     * @return string
     */
    private function renderSelect(AST\SelectPart $selectPart)
    {
        $retVal = 'SELECT ';

        $fields = array();

        foreach($selectPart->getSelectFields() AS $selectField)
        {
            $fields[] = $this->renderSelectField($selectField);
        }

        $retVal .= implode(', ', $fields);

        return $retVal;
    }

    /**
     * @param AST\SelectFieldInterface $selectField
     * @return string
     */
    private function renderSelectField(AST\SelectFieldInterface $selectField)
    {
        $retVal = null;

        // SUBQUERIES; FUNCTIONS/AGGREGATES; SUBQUERIES (EVERYTHING HAVING AN ALIAS)
        if($selectField instanceof AST\SelectFieldInterface)
        {
            if($selectField instanceof AST\Subquery)
            {
                $retVal = '(' . $this->renderQuery($selectField->getQuery()) . ')';
            }
            elseif($selectField instanceof AST\TypeofSelectPart)
            {
                $retVal = $this->renderTypeofSelectPart($selectField);
            }
            elseif($selectField instanceof AST\SelectFunction)
            {
                $retVal = $this->renderFunction($selectField->getFunction());
            }
            else
            {
                $retVal = $selectField->getName();
            }
        }

        if($selectField instanceof AST\CanHazAliasInterface)
        {
            $retVal .= $this->renderAlias($selectField);
        }

        return $retVal;
    }

    /**
     * @param AST\TypeofSelectPart $typeofSelectPart
     * @return string
     */
    private function renderTypeofSelectPart(AST\TypeofSelectPart $typeofSelectPart)
    {
        $retVal = 'TYPEOF ' . $typeofSelectPart->getSobjectName();

        foreach($typeofSelectPart->getConditions() AS $condition)
        {
            $retVal .= ' WHEN ' . $condition->getSobjectFieldname() . ' THEN ';

            $fields = array();

            foreach($condition->getSelectPart()->getSelectFields() AS $selectField)
            {
                $fields[] = $this->renderSelectField($selectField);
            }

            $retVal .= implode(', ', $fields);
        }

        $else = $typeofSelectPart->getElse();

        if(null !== $else)
        {
            $retVal .= ' ELSE ';

            $fields = array();

            foreach($typeofSelectPart->getElse()->getSelectFields() AS $selectField)
            {
                $fields[] = $this->renderSelectField($selectField);
            }

            $retVal .= implode(', ', $fields);
        }

        return $retVal . ' END';
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\FromPart $fromPart
     * @return string
     */
    private function renderFrom(AST\FromPart $fromPart)
    {
        $retVal = ' FROM ' . $fromPart->getFromObject();

        if($fromPart instanceof AST\CanHazAliasInterface)
        {
            $retVal .= $this->renderAlias($fromPart->getAlias());
        }
        return $retVal;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\CanHazAliasInterface $alias
     * @return string
     */
    private function renderAlias(AST\CanHazAliasInterface $alias)
    {
        if($alias->hasAlias())
        {
            return ' AS ' . $alias->getAlias()->getName();
        }
        return '';
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\WherePart $wherePart
     * @return string
     */
    private function renderWhere(AST\WherePart $wherePart = null)
    {
        if(null === $wherePart)
        {
            return '';
        }

        return ' WHERE ' . $this->renderLogicalGroup($wherePart->getLogicalGroup());
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\WithPart $withPart
     * @return string
     */
    private function renderWith(AST\WithPart $withPart = null)
    {
        if(null === $withPart)
        {
            return '';
        }

        return ' WITH ' . $withPart->getWith() . ' ' . $this->renderLogicalGroup($withPart->getLogicalGroup());
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\GroupPart
     *
     * @return string
     */
    private function renderGroup(AST\GroupByExpression $groupPart = null)
    {
        if(null === $groupPart)
        {
            return '';
        }

        $retVal = ' GROUP BY';

        if($groupPart->getIsCube())
        {
            $retVal .= ' CUBE (';
        }
        elseif($groupPart->getIsRollup())
        {
            $retVal .= ' ROLLUP (';
        }
        else
        {
            $retVal .= ' ';
        }

        $retVal .= $this->renderGroupFields($groupPart->getGroupFields());

        if($groupPart->getIsCube() || $groupPart->getIsRollup())
        {
            $retVal .= ')';
        }
        return $retVal;
    }

    /**
     * @param array<AST\GroupableInterface> $groupFields
     * @return string
     */
    private function renderGroupFields(array $groupFields)
    {
        $parts = array();

        /** @var $groupField AST\GroupableInterface  */
        foreach($groupFields AS $groupField)
        {
            $parts[] = $this->renderGroupField($groupField);
        }

        return implode(', ', $parts);
    }

    private function renderGroupField(AST\GroupableInterface $groupField)
    {
        if($groupField instanceof AST\GroupByFunction)
        {
            return $this->renderFunction($groupField->getFunction());
        }
        return $groupField->getName();
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\HavingPart
     *
     * @return string
     */
    private function renderHaving(AST\HavingPart $havingPart = null)
    {
        if(null === $havingPart)
        {
            return '';
        }
        return ' HAVING ' . $this->renderLogicalGroup($havingPart->getLogicalGroup());
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\OrderPart $orderPart
     * @return string
     */
    private function renderOrder(AST\OrderPart $orderPart = null)
    {
        if(null === $orderPart)
        {
            return '';
        }
        return ' ORDER BY ' . $this->renderOrderByFields($orderPart->getOrderFields());
    }

    /**
     * @param null $offset
     * @return string
     */
    private function renderOffset($offset = null)
    {
        if(null === $offset)
        {
            return '';
        }
        return ' OFFSET ' . $offset;
    }

    /**
     * @param null $limit
     * @return string
     */
    private function renderLimit($limit = null)
    {
        if(null === $limit)
        {
            return '';
        }
        return ' LIMIT ' . $limit;
    }

    /**
     * @param array<AST\SortableInterface> $orderFields
     * @return string
     */
    private function renderOrderByFields(array $orderFields)
    {
        $parts = array();

        foreach($orderFields AS $orderField)
        {
            $parts[] = $this->renderOrderByField($orderField);
        }

        return implode(', ', $parts);
    }

    /**
     * May pass function/aggregate function and/or plain fieldnames.
     *
     * @param AST\OrderByFieldInterface $orderByField
     * @return string
     */
    private function renderOrderByField(AST\OrderByFieldInterface $orderByField)
    {
        $retVal = '';

        if($orderByField instanceof AST\OrderByFunction)
        {
            $retVal .= $this->renderFunction($orderByField->getFunction());
        }

        // PLAIN FIELD
        else
        {
            $retVal .= $orderByField->getName();
        }

        if($dir = $orderByField->getDirection())
        {
            $retVal .= ' ' . $dir;
        }

        if($nulls = $orderByField->getNulls())
        {
            $retVal .=  ' ' . $nulls;
        }

        return $retVal;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalGroup $logicalGroup
     * @throws \RuntimeException
     * @return string
     */
    private function renderLogicalGroup(AST\LogicalGroup $logicalGroup)
    {
        $junctions = $logicalGroup->getJunctions();

        $size = count($junctions);

        $parts = array();

        for($i = 0; $i < $size; $i++)
        {
            $parts2 = array();

            /** @var $junction \Codemitte\ForceToolkit\Soql\AST\LogicalJunction */
            $junction = $junctions[$i];

            if($i > 0)
            {
                if(null === ($operator = $junction->getOperator()))
                {
                    throw new \RuntimeException('Malformed query: Missing junction operator AND/OR in condition.');
                }
                $parts2[] = $operator;
            }

            if($junction->getIsNot())
            {
                $parts2[] = 'NOT';
            }

            $parts2[] = $this->renderCondition($junction->getCondition());

            $parts[] = implode(' ', $parts2);
        }
        return implode(' ', $parts);
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\LogicalConditionInterface $condition
     * @return string
     */
    private function renderCondition(AST\LogicalConditionInterface $condition)
    {
        $retVal = null;

        // SUBGROUP ...
        if($condition instanceof AST\LogicalGroup)
        {
            $retVal = '(' . $this->renderLogicalGroup($condition) . ')';
        }
        else
        {
            /** @var $condition \Codemitte\ForceToolkit\Soql\AST\LogicalCondition */
            $retVal =
                  (($lft = $condition->getLeft()) ? $this->renderConditionLeft($lft) : '').
                  ' ' . $condition->getOperator() .
                  ' ' . $this->renderConditionRight($condition->getRight());
        }
        return $retVal;
    }

    /**
     * May take the following argument types:
     * - SoqlName (arbitrary fieldname)
     * - Where-/Having-(aggregate)function
     * @param mixed $expression
     * @return string
     */
    private function renderConditionLeft(AST\ConditionLeftOperandInterface $expression)
    {
        if($expression instanceof AST\ConditionLeftOperandFunctionInterface)
        {
            return $this->renderFunction($expression->getFunction());
        }
        return $expression->getFieldname();
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\Functions\SoqlFunctionInterface $function
     * @return string
     */
    private function renderFunction(Functions\SoqlFunctionInterface $function)
    {
        $retVal = $function->getName() . '(';

        $args = array();

        foreach($function->getArguments() AS $argument)
        {
            $args[] = $this->renderFunctionArgument($argument);
        }

        $retVal .= implode(', ', $args);

        return $retVal . ')';
    }

    /**
     * @param $argument
     * @return string
     */
    private function renderFunctionArgument($argument)
    {
        if($argument instanceof AST\Functions\SoqlFunctionInterface)
        {
            return $this->renderFunction($argument);
        }
        return (string)$argument;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\ComparableInterface $comparable
     * @return string
     */
    private function renderConditionRight(AST\ComparableInterface $comparable)
    {
        if($comparable instanceof AST\Subquery)
        {
            return '(' . $this->renderQuery($comparable) . ')';
        }

        if($comparable instanceof AST\NamedVariable)
        {
            return $this->renderNamedVariable($comparable);
        }

        if($comparable instanceof AST\AnonymousVariable)
        {
            return $this->renderAnonymousVariable($comparable);
        }

        return (string)$comparable;
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\NamedVariable $comparable
     * @throws \RuntimeException
     * @return string
     */
    private function renderNamedVariable(AST\NamedVariable $comparable)
    {
        $name = $comparable->getValue();

        $value = null;

        if(array_key_exists($name, $this->parameters))
        {
            $value = $this->parameters[$name];
        }
        else
        {
            throw new \RuntimeException(sprintf('Variable with name "%s" was never bound.', $name));
        }

        $type = $this->typeFactory->create($value);

        if(null === $type)
        {
            throw new \RuntimeException(sprintf('Type error on param with index "%s": Type could not be resolved.', $name));
        }

        return $type->toSOQL();
    }

    /**
     * @param \Codemitte\ForceToolkit\Soql\AST\AnonymousVariable $comparable
     * @throws \RuntimeException
     * @return string
     */
    private function renderAnonymousVariable(AST\AnonymousVariable $comparable)
    {
        $ind = $this->varIndex ++;

        $value = null;

        if(array_key_exists($ind, $this->parameters))
        {
            $value = $this->parameters[$ind];
        }
        else
        {
            throw new \RuntimeException(sprintf('Variable with index "%d" was never bound.', $ind));
        }

        $type = $this->typeFactory->create($value);

        if(null === $type)
        {
            throw new \RuntimeException(sprintf('Type error on param with index "%d": Type could not be resolved.', $ind));
        }

        return $type->toSOQL();
    }
}
