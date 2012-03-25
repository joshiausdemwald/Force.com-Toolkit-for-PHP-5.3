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

use \Traversable;
use \InvalidArgumentException;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Common
 * @subpackage Collection
 */
class GenericList extends AbstractList
{
    /**
     * @var array
     */
    private $values = array();

    /**
     * Constructor.
     *
     * @param $values
     */
    public function __construct($values)
    {
        $this->addAll($values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, integer
     * 0 on failure.
     */
    public function key()
    {
        return key($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize()
    {
        return serialize($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $this->values = unserialize($serialized);
    }

    /**
     * get()
     *
     * @param int $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->values[$key];
    }

    /**
     *
     * @param mixed $value
     *
     * @return void
     */
    public function add($value)
    {
        $this->values[] = $value;
    }

    /**
     * addAll()
     *
     * @param array|ListInterface|Traversable $values
     *
     * @return void
     */
    public function addAll($values)
    {
        $toMerge = null;

        if(is_array($values))
        {
            $toMerge = array_values($values);
        }
        elseif($values instanceof ListInterface)
        {
            $toMerge = $values->toArray();
        }
        elseif($values instanceof Traversable)
        {
            $toMerge = array();

            foreach($values AS $value)
            {
                $toMerge[] = $value;
            }
        }
        else
        {
            throw new InvalidArgumentException('Values $values must be an array of instanceof \Traversable.');
        }
        $this->values = array_merge($this->values, $toMerge);
    }

    /**
     * has()
     *
     * @param int $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * remove()
     *
     * @param int $key
     *
     * @return mixed
     */
    public function remove($key)
    {
        $retVal = $this->get($key);

        unset($this->values[$key]);

        return $retVal;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function replace($key, $value)
    {
        $retVal = $this->get($key);

        $this->values[$key] = $value;

        return $retVal;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * toArray()
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * Checks if the specified value exists in the current
     * collection.
     *
     * @param $value
     * @return bool
     */
    public function containsValue($value)
    {
        return in_array($value, $this->values);
    }
}
