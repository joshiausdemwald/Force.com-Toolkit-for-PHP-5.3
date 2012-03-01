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
class Tokenizer implements TokenizerInterface
{
    /**
     * @var array
     */
    private $tokenDefinitions;

    /**
     * Constructor.
     *
     * @param $input
     */
    public function __construct()
    {
        $this->tokenDefinitions = array
        (
            // STRING LITERAL
            '\'' => function($stream, $pos, $buf, $tokens)
            {
                $literal = '';

                $valid = false;

                // BEGIN AT SECOND CHAR, FIRST IS A "'"
                for($pos++; $len = strlen($stream), $pos < $len; $pos ++)
                {
                    $orig_pos = $pos;

                    $char = $stream[$pos];

                    $prevChar = $stream[$pos-1];

                    if(TokenizerInterface::LITERAL_TERMINATOR === $char && TokenizerInterface::ESCAPE_CHAR !== $prevChar)
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
                    throw new TokenException(sprintf('Error parsing SOQL-Query "%s": Unterminated string literal at col %s.', $stream, $pos));
                }

                $tokens[] = array(
                    TokenizerInterface::TOKEN_LITERAL,
                    $literal,
                    $orig_pos
                );
            },
            ':' => function($stream, $pos, $buf, $tokens)
            {
                $rest = substr($stream, $pos + 1);

                if(preg_match('#^\w+?\b#', $rest, $result))
                {
                    $name = $result[0];

                    $tokens[] = array(
                        TokenizerInterface::TOKEN_NAMED_VARIABLE,
                        $name,
                        $pos
                    );

                    $pos += strlen($name);
                }
            },
            '?' => function($stream, $pos, $buf, $tokens)
            {
                $tokens[] = array(
                    TokenizerInterface::TOKEN_ANON_VARIABLE,
                    null,
                    $pos
                );
                $pos += 1;
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
        $buf = '';

        $tokens = array();

        for($i = 0; $len = strlen($input), $i < $len; $i ++)
        {
            $previousChar = null;

            $currentChar = $input[$i];

            if(
                TokenizerInterface::ESCAPE_CHAR !== $previousChar &&
                array_key_exists($currentChar, $this->tokenDefinitions)
            ) {
                if(strlen($buf) > 0)
                {
                    $tokens[] = array(
                        TokenizerInterface::TOKEN_SOQL_PART,
                        $buf,
                        $i - strlen($buf)
                    );
                    $buf = '';
                }

                call_user_func_array($this->tokenDefinitions[$currentChar], array(
                    & $input,
                    & $i,
                    & $buf,
                    & $tokens

                ));
            }
            else
            {
                $buf .= $currentChar;
            }

            $previousChar = $currentChar;
        }
        return $tokens;
    }
}

