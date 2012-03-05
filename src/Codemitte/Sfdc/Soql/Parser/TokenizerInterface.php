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
 * TokenizerInterface
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soql
 */
interface TokenizerInterface
{
    const TOKEN_EXPRESSION                   = 'EXPRESSION';

    const TOKEN_SOQL_PART                    = 'SOQL_PART';

    const TOKEN_LITERAL                      = 'LITERAL';

    const ESCAPE_CHAR                        = '\\';

    const LITERAL_TERMINATOR                 = '\'';

    /**
     * Returns a list of tokens for a "raw" soql query
     * string.
     *
     * @abstract
     *
     * @param string $input
     *
     * @return array $tokens
     */
    public function getTokens($input);

    /**
     * Adds a mark at the specified position of the
     * input stream.
     *
     * @abstract
     *
     * @param string $stream
     * @param int $position
     * @param string $markerl
     * @param string $markerr
     */
    public function addMark($stream, $position, $markerl = '--->', $markerr = '<---');

    /**
     * Registers a token definition.
     *
     * @abstract
     *
     * @param string $token
     * @param callable$definition
     */
    public function registerTokenDefinition($token, $definition);

    /**
     * Registers a bunch of token definitions.
     *
     * @abstract
     * @param array $definitions
     */
    public function registerTokenDefinitions(array $definitions);
}
