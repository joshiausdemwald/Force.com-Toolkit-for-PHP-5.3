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

namespace Codemitte\Soap\Mapping;


use Codemitte\Common\Collection\GenericMap;
use Codemitte\Soap\Mapping\ClassInterface;

/**
 * GenericResult
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 */
class GenericResult extends GenericMap implements ClassInterface
{
    /**
     * Constructor.
     *
     * @param \stdClass|array $values
     */
    public function __construct($values = array())
    {
        parent::__construct($values);
    }

    /**
     * Gracefully returns NULL if key does not exist.
     *
     * @override
     *
     * @param scalar $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if(parent::contains($key))
        {
            return parent::get($key);
        }
        return null;
    }
}