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

use \stdClass;
use \Traversable;
use \InvalidArgumentException;
use \ArrayIterator;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Common
 * @subpackage Collection
 */
class GenericMap extends AbstractMap
{
    /**
     * @var array
     */
    private $container = array();

    /**
     * Constructor.
     *
     * @param array|MapInterface|Traversable $container
     * @param string $iteratorClass
     */
    public function __construct($container = array())
    {
        $this->putAll($container);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize()
    {
        return serialize($this->container);
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
        $this->container = unserialize($serialized);
    }

    /**
     *
     * @param scalar $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->container[$key];
    }

    /**
     *
     *
     * @param scalar $key
     * @param mixed $value
     *
     * @return void
     */
    public function put($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * putAll()
     *
     * @param array|MapInterface|Traversable $values
     *
     * @return void
     */
    public function putAll($values)
    {
        $toMerge = null;

        if(is_array($values))
        {
            $toMerge = $values;
        }
        elseif($values instanceof stdClass)
        {
            $toMerge = (array)$values;
        }
        elseif($values instanceof MapInterface)
        {
            $toMerge = $values->toArray();
        }
        elseif($values instanceof Traversable)
        {
            $toMerge = array();

            foreach($values AS $key => $value)
            {
                $toMerge[$key] = $value;
            }
        }
        else
        {
            throw new InvalidArgumentException('Values $values must be an array or instanceof \Traversable, "' . gettype($values) . '" given.');
        }
        $this->container = array_merge($this->container, $toMerge);
    }

    /**
     * @param scalar $key
     *
     * @return bool
     */
    public function contains($key)
    {
        return array_key_exists($key, $this->container);
    }

    /**
     * @param scalar $key
     *
     * @return mixed
     */
    public function remove($key)
    {
        $retVal = $this->container[$key];

        unset($this->container[$key]);

        return $retVal;
    }

    /**
     * toArray()
     *
     * @return array
     */
    public function toArray()
    {
        return $this->container;
    }

    /**
     *
     * @return ListInterface
     */
    public function getKeys()
    {
        return array_keys($this->container);
    }

    /**
     * getValues()
     *
     * @return ListInterface
     */
    public function getValues()
    {
        return new GenericList(array_values($this->toArray()));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing Iterator or
     * Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->container);
    }
}