<?php
/**
 * Copyright (C) 2012 code mitte GmbH - Zeughausstr. 28-38 - 50667 Cologne/Germany
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so, subject
 * to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Codemitte\Sfdc\Soql\Parser;

use \Traversable;

use Codemitte\Sfdc\Soql\Type\TypeFactory;
use Codemitte\Sfdc\Soql\Type\TypeInterface;
use Codemitte\Sfdc\Soql\Type\Expression;

/**
 * QueryParser
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soql
 */
class QueryParser implements QueryParserInterface
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     * @var TypeFactory
     */
    private $typeFactory;

    /**
     * Constructor.
     *
     * @param TokenizerInterface|null $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer = null, TypeFactory $typeFactory = null)
    {
        if(null === $tokenizer)
        {
            $tokenizer = new QueryTokenizer();
        }
        $this->tokenizer = $tokenizer;

        if(null === $typeFactory)
        {
            $typeFactory = new TypeFactory();
        }
        $this->typeFactory = $typeFactory;
    }

    /**
     *
     * @param string $soql
     * @param array $parameters
     * @return string
     */
    public function parse($soql, array $parameters = array())
    {
        $tokens = $this->tokenizer->getTokens($soql);

        $output = '';

        $previous_token = null;

        foreach($tokens AS $i => $token)
        {
            $t_type  = $token[0];
            $t_value = $token[1];
            $t_col   = $token[2];

            switch($t_type)
            {
                case TokenizerInterface::TOKEN_SOQL_PART:
                case TokenizerInterface::TOKEN_EXPRESSION;
                    $output .= $this->getParameter($t_value, $parameters)->toSOQL();
                    break;

                case TokenizerInterface::TOKEN_LITERAL:
                    $output .= '\'' . $t_value . '\'';
                    break;
                case TokenizerInterface::TOKEN_SOQL_PART:

                    $output .= $t_value;

                    break;
            }
            $previous_token = $token;
        }
        return $output;
    }

    /**
     * getParameter()
     *
     * @throws ParseException
     *
     * @param string $name
     * @param array $params
     *
     * @return mixed $param
     */
    private function getParameter($name, array $params)
    {
        if( ! array_key_exists($name, $params))
        {
            // "COMPLEX" Expression, @TODO: Check if it is a variable
            return new Expression($name);
        }

        $param = $params[$name];

        if( ! $param instanceof TypeInterface)
        {
            $param = $this->typeFactory->create($param);
        }

        if( ! $param instanceof TypeInterface)
        {
            throw new ParseException('No Salesforce compatible type given. Param "%s" must implement \Codemitte\Sfdc\Soql\Type\TypeInterface.');
        }
        return $param;
    }
}
