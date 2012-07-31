<?php
namespace Codemitte\Sfdc\Soql\Renderer;

use
    Codemitte\Sfdc\Soql\AST,
    Codemitte\Sfdc\Soql\Type\TypeFactory;


class QueryRenderer
{
    /**
     * @var TypeFactory
     */
    private $typeFactory;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var int
     */
    private $varIndex;

    /**
     * @param \Codemitte\Sfdc\Soql\Type\TypeFactory $typeFactory
     */
    public function __construct(TypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\Query $query
     * @param array $parameters
     * @return string
     */
    public function render(AST\Query $query, array $parameters = array())
    {
        $this->parameters = $parameters;

        $this->varIndex = 0;

        return $this->renderQuery($query, $parameters);
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\Query $query
     * @return string
     */
    private function renderQuery(AST\Query $query)
    {
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
     * @param \Codemitte\Sfdc\Soql\AST\SelectPart $selectPart
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
     * @param \Codemitte\Sfdc\Soql\AST\SelectableInterface $selectField
     * @return string
     */
    private function renderSelectField(AST\SelectableInterface $selectField)
    {
        $retVal = null;

        if($selectField instanceof AST\Subquery)
        {
            $retVal = '(' . $this->renderQuery($selectField->getQuery()) . ')';
        }
        else
        {
            $retVal = $selectField->getName();
        }

        return $retVal . $this->renderAlias($selectField->getAlias());
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\FromPart $fromPart
     * @return string
     */
    private function renderFrom(AST\FromPart $fromPart)
    {
        return ' FROM ' .
            $fromPart->getFromObject() .
            $this->renderAlias($fromPart->getAlias());
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\Alias $alias
     * @return string
     */
    private function renderAlias(AST\Alias $alias = null)
    {
        if(null === $alias)
        {
            return '';
        }
        return ' AS ' . $alias->getName();
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\WherePart $wherePart
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
     * @param \Codemitte\Sfdc\Soql\AST\WithPart $withPart
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
     * @param \Codemitte\Sfdc\Soql\AST\GroupPart
     *
     * @return string
     */
    private function renderGroup(AST\GroupPart $groupPart = null)
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
            $retVal = ' ';
        }

        $retVal .= $this->renderGroupFields($groupPart->getGroupFields());

        if($groupPart->getIsCube() || $groupPart->getIsRollup())
        {
            $retVal .= ')';
        }
        return $retVal;
    }

    /**
     * @param array<AST\GroupField> $groupFields
     * @return string
     */
    private function renderGroupFields(array $groupFields)
    {
        $parts = array();

        /** @var $groupField AST\GroupField  */
        foreach($groupFields AS $groupField)
        {
            $parts[] = $groupField->getName();
        }

        return implode(', ', $parts);
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\HavingPart
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
     * @param \Codemitte\Sfdc\Soql\AST\OrderPart $orderPart
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
     * @param \Codemitte\Sfdc\Soql\AST\SortableInterface $orderField
     * @return string
     */
    private function renderOrderByField(AST\SortableInterface $orderField)
    {
        /** @var $orderField AST\OrderByField  */

        $retVal = $orderField->getName();

        if(null !== $orderField->getDirection())
        {
            $retVal .= ' ' . $orderField->getDirection();
        }

        if(null !== $orderField->getNulls())
        {
            $retVal .= ' ' . $orderField->getNulls();
        }
        return $retVal;
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\LogicalGroup $logicalGroup
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

            /** @var $junction \Codemitte\Sfdc\Soql\AST\LogicalJunction */
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
                $part2[] = 'NOT';
            }

            $parts2[] = $this->renderCondition($junction->getCondition());

            $parts[] = implode(' ', $parts2);
        }
        return implode(' ', $parts);
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\LogicalConditionInterface $condition
     * @return string
     */
    private function renderCondition(AST\LogicalConditionInterface $condition)
    {
        $retVal = null;

        if($condition instanceof AST\LogicalGroup)
        {
            $retVal = '(' . $this->renderLogicalGroup($condition) . ')';
        }
        else
        {
            /** @var $condition \Codemitte\Sfdc\Soql\AST\LogicalCondition */
            $retVal =
                  $this->renderExpression($condition->getLeft()) .
                  ' ' . $condition->getOperator() .
                  ' ' . $this->renderComparable($condition->getRight());
        }
        return $retVal;
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\SoqlExpression|\Codemitte\Sfdc\Soql\AST\SoqlExpressionInterface $expression
     * @return string
     * @todo: CLEANUP, see RenderExpression
     */
    private function renderExpression(AST\SoqlExpressionInterface $expression = null)
    {
        if(null === $expression)
        {
            return '';
        }
        if($expression instanceof \Codemitte\Sfdc\Soql\AST\SoqlFunction)
        {
            return $this->renderFunction($expression);
        }
        return $expression->getExpression();
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\SoqlFunction $function
     * @return string
     * @todo: CLEANUP, see RenderExpression
     */
    private function renderFunction(AST\SoqlFunction $function)
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
     * @param \Codemitte\Sfdc\Soql\AST\SoqlFunctionArgumentInterface $argument
     * @return string
     * @todo: CLEANUP, see RenderExpression
     */
    private function renderFunctionArgument(AST\SoqlFunctionArgumentInterface $argument)
    {
        if($argument instanceof \Codemitte\Sfdc\Soql\AST\SoqlFunction)
        {
            return $this->renderFunction($argument);
        }
        return $argument->getExpression();
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\ComparableInterface $comparable
     * @return string
     */
    private function renderComparable(AST\ComparableInterface $comparable)
    {
        if($comparable instanceof \Codemitte\Sfdc\Soql\AST\Subquery)
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

        return $comparable->getValue();
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\NamedVariable $comparable
     * @throws \RuntimeException
     * @return string
     */
    private function renderNamedVariable(AST\NamedVariable $comparable)
    {
        $this->varIndex ++;

        $value = null;

        if(isset($this->parameters[($name = $comparable->getValue())]))
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
     * @param \Codemitte\Sfdc\Soql\AST\AnonymousVariable $comparable
     * @throws \RuntimeException
     * @return string
     */
    private function renderAnonymousVariable(AST\AnonymousVariable $comparable)
    {
        $ind = $this->varIndex ++;

        $value = null;

        if(isset($this->parameters[$ind]))
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
