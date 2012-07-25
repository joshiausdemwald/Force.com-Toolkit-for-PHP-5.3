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
use Codemitte\Sfdc\Soql\Tokenizer\TokenizerInterface;
use Codemitte\Sfdc\Soql\Tokenizer\TokenizerException;
use Codemitte\Sfdc\Soql\Tokenizer\TokenType;

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
     * @var string
     */
    private $output;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $indexedParameters;

    /**
     * @var int
     */
    private $level;

    /**
     * @var int
     */
    private $varIndex;

    /**
     * Constructor.
     *
     * @param TokenizerInterface|null $tokenizer
     * @param \Codemitte\Sfdc\Soql\Type\TypeFactory $typeFactory
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
     * @param string $soql
     * @param array $parameters
     *
     * @return string
     * @return string|void
     */
    public function parse($soql, array $parameters = array())
    {
        $this->tokenizer->setInput($soql);

        $this->output               = '';

        $this->parameters           = $parameters;

        $this->indexedParameters    = array_values($this->parameters);

        $this->varIndex             = 0;

        $this->level                = 0;

        return $this->loop();
    }

    /**
     * The "Main loop"
     */
    private function loop()
    {
        while(true)
        {
            if($this->tokenizer->is(TokenType::EOF))
            {
                return $this->output;
            }
            elseif($this->tokenizer->is(TokenType::SOQL_FUNCTION))
            {
                $this->parseSoqlFunction();
            }
            elseif($this->tokenizer->is(TokenType::ANON_VARIABLE))
            {
                $this->parseAnonVariable();
            }
            elseif($this->tokenizer->is(TokenType::NAMED_VARIABLE))
            {
                $this->parseNamedVariable();
            }
            elseif($this->tokenizer->is(TokenType::RIGHT_PAREN))
            {
                $this->parseRightParen();
            }
            elseif($this->tokenizer->is(TokenType::LEFT_PAREN))
            {
                $this->parseLeftParen();
            }
            else
            {
                $this->parseArbitrarySoql();
            }
        }
    }

    /**
     * Parse arbitrary soql parts.
     */
    private function parseArbitrarySoql()
    {
        $this->output .= $this->tokenizer->getTokenValue();

        $this->tokenizer->readNextToken();
    }

    /**
     * SELECT ... FROM ... WHERE ... GROUP BY ... HAVING ... OFFSET ... LIMIT ...
     * @TODO: VALIDATE, SPLIT AND MAP TYPES TO INCOMING VARIABLES (INTROSPECT)
     */
    private function parseSelect()
    {
        $this->output .= 'SELECT';

        $this->tokenizer->readNextToken();
    }

    private function parseSoqlFunction()
    {
        $this->output .= $this->tokenizer->getTokenValue();

        $this->tokenizer->expect(TokenType::SOQL_FUNCTION);

        if( ! $this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            throw new ParseException(sprintf('Expected left parenthesis, "%s" found', $this->tokenizer->getTokenValue()), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
        }
    }

    private function parseLeftParen()
    {
        $this->tokenizer->expect(TokenType::LEFT_PAREN);

        $this->level ++;

        $this->output .= '(';
    }

    private function parseRightParen()
    {
        $this->tokenizer->expect(TokenType::RIGHT_PAREN);

        $this->level --;

        $this->output .= ')';
    }

    /**
     * getNamedParameter()
     *
     * @throws ParseException
     *
     * @return mixed $param
     */
    private function parseNamedVariable()
    {
        $soqlPart = $this->tokenizer->getTokenValue();

        $name = substr($soqlPart, 1);

        if( ! array_key_exists($name, $this->parameters))
        {
            throw new ParseException(sprintf('Named variable "%s" was never bound.', $soqlPart), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
            // "COMPLEX" Expression, @TODO: Check if it is a variable
            // return new Expression($name);
        }

        $param = $this->parameters[$name];

        if( ! $param instanceof TypeInterface)
        {
            $param = $this->typeFactory->create($param);
        }

        if( ! $param instanceof TypeInterface)
        {
            throw new ParseException(sprintf('No Salesforce compatible type given. Param "%s" must implement \Codemitte\Sfdc\Soql\Type\TypeInterface.', $soqlPart), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
        }
        $this->output .= $param->toSOQL();

        $this->tokenizer->readNextToken();
    }

    /**
     * getIndexedParameter()
     *
     * @throws ParseException
     *
     * @return mixed $param
     */
    private function parseAnonVariable()
    {
        $this->tokenizer->expect(TokenType::ANON_VARIABLE);

        if( ! array_key_exists($this->varIndex, $this->indexedParameters))
        {
            throw new ParseException(sprintf('Anonymous variable with index "%s" was never bound.', $this->varIndex), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
            // "COMPLEX" Expression, @TODO: Check if it is a variable
            // return new Expression($name);
        }

        $param = $this->indexedParameters[$this->varIndex];

        if( ! $param instanceof TypeInterface)
        {
            $param = $this->typeFactory->create($param);
        }

        if( ! $param instanceof TypeInterface)
        {
            throw new ParseException(sprintf('No Salesforce compatible type given. Param "%s" must implement \Codemitte\Sfdc\Soql\Type\TypeInterface.', $soqlPart), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
        }
        $this->output .= $param->toSOQL();

        $this->tokenizer->readNextToken();
    }
}
