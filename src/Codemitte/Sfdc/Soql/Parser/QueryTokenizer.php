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

use InvalidArgumentException;

/**
 * Tokenizer
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soql
 */
class QueryTokenizer implements TokenizerInterface
{
    /**
     * @var array<Callable>
     */
    private $tokenDefinitions;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this->registerTokenDefinitions(array
        (
            // STRING LITERAL
            '\'' => array($this, 'parseLiteral'),

            // EXPRESSION/VARIABLE
            ':' => array($this, 'parseExpression')
        ));
    }

    /**
     * Registers a token definition.
     *
     * @param callable $definition
     * @param string $token
     */
    public function registerTokenDefinition($definition, $token)
    {
        if( ! is_callable($definition))
        {
            throw new InvalidArgumentException('registerTokenDefinition(): $definition must be a valid PHP-callable.');
        }
        $this->tokenDefinitions[$token] = $definition;
    }

    /**
     * Registers a bunch of token definitions.
     *
     * @param array <callable> $definitions
     */
    public function registerTokenDefinitions(array $definitions)
    {
        array_walk($definitions, array($this, 'registerTokenDefinition'));
    }

    /**
     * Returns a list of token.
     *
     * @param string $input
     *
     *
     * @return array $tokens
     */
    public function getTokens($input)
    {
        $tokens = array();

        $previousChar = null;

        $buf = '';

        for($i = 0; $len = strlen($input), $i < $len; $i ++)
        {
            $currentChar = $input[$i];

            if(
                // MUST BE PRECEDED BY ANY NON-WORD CHARACTER
                preg_match('#[\W]#', $previousChar) &&
                $previousChar !== TokenizerInterface::ESCAPE_CHAR &&
                array_key_exists($currentChar, $this->tokenDefinitions)
            ) {

                if(strlen($buf) > 0)
                {
                    $tokens[] = array(
                        TokenizerInterface::TOKEN_SOQL_PART,
                        $buf,
                        $i - strlen($buf)
                    );
                }

                call_user_func_array($this->tokenDefinitions[$currentChar], array(
                    & $input,  // stream
                    & $i,      // pos
                    & $tokens, // array tokens
                    $this      // tokenizerInterface
                ));

                $buf = '';
            }
            else
            {
                $buf .= $currentChar;
            }
            $previousChar = $currentChar;
        }

        if(strlen($buf) > 0)
        {
            $tokens[] = array(
                TokenizerInterface::TOKEN_SOQL_PART,
                $buf,
                $i - strlen($buf)
            );
        }

        return $tokens;
    }

    /**
     * Adds a mark at the specified position of the
     * input stream.
     *
     * @param string $stream
     * @param integer $position
     * @param string $marker
     */
    public function addMark($stream, $position, $markerl = '--->', $markerr = '<---')
    {
        return substr($stream, 0, $position - 1) . $markerl . $stream[$position - 1] . $markerr . substr($stream, $position);
    }

    /**
     * TokenParser 1: Literals
     * @static
     * @param $stream
     * @param $pos
     * @param $tokens
     * @param $tokenizer
     * @throws TokenException
     */
    public function parseLiteral($stream, $pos, array $tokens, TokenizerInterface $tokenizer)
    {
        $literal = '';

        $valid = false;

        $orig_pos = $pos;

        // BEGIN AT SECOND CHAR, FIRST IS A "'"
        for($pos++; $len = strlen($stream), $pos < $len; $pos ++)
        {
            $char = $stream[$pos];

            $prevChar = $stream[$pos-1];

            if(
                TokenizerInterface::LITERAL_TERMINATOR === $char &&
                TokenizerInterface::ESCAPE_CHAR !== $prevChar
            ) {
                $valid = true;
                break;
            }
            else
            {
                $literal .= $char;
            }
        }

        if( ! $valid)
        {
            throw new TokenException(sprintf('Error parsing SOQL-Query "%s": Unterminated string literal at column %s: "%s".', $tokenizer->addMark($pos,$stream), $pos, $literal));
        }

        $tokens[] = array(
            TokenizerInterface::TOKEN_LITERAL,
            $literal,
            $orig_pos
        );
    }

    /**
     * TokenParser 2: Parses expressions.
     *
     * @param string $stream
     * @param int $pos
     * @param array $tokens
     * @param QueryTokenizer $tokenizer
     * @throws TokenException
     */
    public function parseExpression($stream, $pos, array $tokens, TokenizerInterface $tokenizer)
    {
        $orig_pos = $pos;

        $valid = false;

        $expression = '';

        $len = strlen($stream);

        $end = $len - 1;

        // BEGIN AT SECOND CHAR, FIRST IS A "'"
        for($pos++; $pos < $len; $pos ++)
        {
            $char = $stream[$pos];

            // END OF EXPRESSION
            if(preg_match('#[\W]#', $char))
            {
                if(0 === strlen($expression))
                {
                    throw new TokenException(sprintf('Error parsing SOQL-Query "%s": Empty expression at column %s', $tokenizer->addMark($stream, $pos), $pos));
                }
                $pos --;

                $valid = true;

                $tokens[] = array(
                    TokenizerInterface::TOKEN_EXPRESSION,
                    $expression,
                    $pos
                );

                $expression = '';

                break;
            }
            else
            {
                $expression .= $char;
            }
        }

        // END OF LINE
        if(strlen($expression) > 0)
        {
            $valid = true;

            $tokens[] = array(
                TokenizerInterface::TOKEN_EXPRESSION,
                $expression,
                $pos
            );
        }

        if( ! $valid)
        {
            throw new TokenException(sprintf('Error parsing SOQL-Query "%s": Illegal expression at %s.', $stream, $orig_pos));
        }
    }
}