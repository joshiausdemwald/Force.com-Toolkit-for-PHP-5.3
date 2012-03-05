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
     * @var array
     */
    private $tokenDefinitions;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this->tokenDefinitions = array
        (
            // STRING LITERAL
            '\'' => function($stream, $pos, $tokens, $tokenizer)
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
                        TokenizerInterface::ESCAPE_CHAR !== $prevChar)
                    {
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
            },

            // EXPRESSION/VARIABLE
            ':' => function($stream, $pos, $tokens, array $tokenDefinitions, QueryTokenizer $tokenizer)
            {
                $orig_pos = $pos;

                $valid = false;

                $expression = '';

                // BEGIN AT SECOND CHAR, FIRST IS A "'"
                for($pos++; $len = strlen($stream), $pos < $len; $pos ++)
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
                        $char,
                        $pos
                    );
                }

                if( ! $valid)
                {
                    throw new TokenException(sprintf('Error parsing SOQL-Query "%s": Illegal expression at %s.', $stream, $orig_pos));
                }
            }
        );
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
                    & $input,
                    & $i,
                    & $tokens,
                    $this->tokenDefinitions,
                    $this
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
}



