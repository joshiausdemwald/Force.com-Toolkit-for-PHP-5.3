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
namespace Codemitte\Common\Collection;

use \ArrayAccess, \Iterator, \Serializable, \Countable;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Common
 * @subpackage Collection
 */
interface ListInterface extends Iterator, Serializable, Countable, ArrayAccess
{
    /**
     * @abstract
     *
     * @param int $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @abstract
     *
     * @param mixed $value
     *
     * @return void
     */
    public function add($value);

    /**
     * @abstract
     *
     * @param array|\Traversable $values
     *
     * @return void
     */
    public function addAll($values);

    /**
     * @abstract
     * @param int $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * @abstract
     * @param int $key
     *
     * @return mixed
     */
    public function remove($key);

    /**
     * @abstract
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function replace($key, $value);

    /**
     * toArray()
     *
     * @abstract
     *
     * @return array
     */
    public function toArray();

    /**
     * containsValue() checks if a specified
     * value exists in the collection.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function containsValue($value);
}
