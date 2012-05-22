<?php
namespace Codemitte\Sfdc\Soql\Query\Expression;

class ExpressionBuilder implements ExpressionBuilderInterface
{
    const EXPR_NS = 'Codemitte\\Sfdc\\Soql\\Query\\Expression\\Expr';
    const IFACE = 'Codemitte\\Sfdc\\Soql\\Query\\Expression\\ExpressionInterface';

    /**
     * @param $name
     * @param $part
     * @return ExpressionInterface
     */
    public function build($name, $part)
    {
        if(is_string($part))
        {
            $part = array($part);
        }

        $classname = $this->guessExpressionClassname($name);

        foreach($part AS $k => $p)
        {
            if( ! $p instanceof ExpressionInterface)
            {
                $part[$k] = new $classname($p);
            }
        }

        return new ExpressionGroup($name, $part);
    }

    /**
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    private function guessExpressionClassname($name)
    {
        $classname = self::EXPR_NS . '\\' . ucfirst(strtolower($name)) . 'Expression';

        if(class_exists($classname) && is_subclass_of($classname, self::IFACE))
        {
            return $classname;
        }
        throw new \InvalidArgumentException(sprintf('"%s" is not a valid name for any soql query expression.', $name));
    }
}