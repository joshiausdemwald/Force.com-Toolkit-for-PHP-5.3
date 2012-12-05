<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use
    Codemitte\Soap\Mapping\ClassInterface,
    Codemitte\Common\Collection\GenericList,
    Codemitte\Soap\Mapping\GenericResultCollection,
    Codemitte\Common\Collection\MapInterface
;

class QueryResult implements ClassInterface, MapInterface
{
    /**
     * @var string
     */
    private $queryLocator;

    /**
     * @var bool
     */
    private $done;

    /**
     * @var array
     */
    private $records;

    /**
     * @var int
     */
    private $size;

    /**
     * @return string
     */
    public function getQueryLocator()
    {
        return $this->queryLocator;
    }

    /**
     * @return boolean
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * @return \Codemitte\Soap\Mapping\GenericResultCollection
     */
    public function getRecords()
    {
        if(null === $this->records)
        {
            $this->records = array();
        }
        return new GenericResultCollection($this->records);
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->getValues();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->contains($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->put($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            'records' => serialize($this->records),
            'done' => $this->done,
            'queryLocator' => serialize($this->queryLocator),
            'size' => $this->size
        ));
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
        $data = unserialize($serialized);

        $this->records = unserialize($data['records']);
        $this->done = $data['done'];
        $this->queryLocator = unserialize($data['queryLocator']);
        $this->size = $data['size'];
    }

    /**
     *
     * @param scalar $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $method = 'get' . ucfirst($key);
        return $this->$method();
    }

    /**
     *
     *
     * @param scalar $key
     * @param mixed $value
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function put($key, $value)
    {
        throw new \BadMethodCallException('Unsupported operation "put()"');
    }

    /**
     *
     * @param array|MapInterface $values
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function putAll($values)
    {
        throw new \BadMethodCallException('Unsupported operation "putAll()"');
    }

    /**
     * @param scalar $key
     *
     * @return bool
     */
    public function contains($key)
    {
        return property_exists($this, $key);
    }

    /**
     * @param scalar $key
     *
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function remove($key)
    {
        throw new \BadMethodCallException('Unsupported operation "remove()"');
    }

    /**
     * getValues()
     *
     *
     * @return ListInterface
     */
    public function getValues()
    {
        return new GenericList(array(
            $this->queryLocator,
            $this->done,
            $this->records,
            $this->size
        ));
    }

    /**
     * toArray()
     *
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'queryLocator' => $this->queryLocator,
            'done' => $this->done,
            'records' => $this->records,
            'size' => $this->size
        );
    }

    /**
     *
     * @return ListInterface
     */
    public function getKeys()
    {
        return new GenericList(array(
            'queryLocator',
            'done',
            'records',
            'size'
        ));
    }
}
