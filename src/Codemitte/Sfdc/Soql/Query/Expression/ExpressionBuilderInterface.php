<?php
namespace Codemitte\Sfdc\Soql\Query\Expression;

interface ExpressionBuilderInterface
{
    public function build($name, $part);
}
