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

/**
 * GenericResult
 */
class GenericResult extends \ArrayObject implements ClassInterface
{
    /**
     * Constructor.
     *
     * @param array|object $properties
     */
    public function __construct($properties)
    {
        parent::__construct($properties, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS, '\\Codemitte\\Soap\\Mapping\\GenericResultIterator');
    }

    /**
     * Returns the keys of the resultset.
     *
     * @return array $key
     */
    public function getKeys()
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * Envoke magic method.
     *
     * @param $key
     * @return mixed
     */
    public function __invoke($key)
    {
        return $this->offsetGet($key);
    }
}
