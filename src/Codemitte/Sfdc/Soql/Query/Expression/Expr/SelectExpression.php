<?php
namespace Codemitte\Sfdc\Soql\Query\Expression\Expr;

use Codemitte\Sfdc\Soql\Query\Expression\ExpressionInterface;

class SelectExpression implements ExpressionInterface
{
    public function getName()
    {
        return 'select';
    }

}
