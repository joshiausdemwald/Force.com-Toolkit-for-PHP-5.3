<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use
    Codemitte\Soap\Hydrator\ResultHydrator
;

class Sobject implements SobjectInterface
{
    /**
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Type\ID
     */
    public $Id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array | null
     */
    private $_keyCache;

    /**
     * @var array | null
     */
    private $_valueCache;

    /**
     * @param array|\stdClass $sobjectType
     * @param array $values
     */
    public function __construct($sobjectType, array $values)
    {
        $this->type = $sobjectType;

        $this->putAll($values);
    }

    /**
     * Returns all the fields of the sobject
     * that are to be (re)set to NULL.
     *
     * Does not regard the Id attribute: This is
     * "standalone" and recognised as NULL if not set.
     *
     * @return array|null
     */
    public function getFieldsToNull()
    {
        $retVal = array();

        foreach($this AS $key => $value)
        {
            if($key === 'Id') continue;

            if(null === $value || '' === $value)
            {
                $retVal[] = $key;
            }
        }

        return count($retVal) > 0 ? $retVal : null;
    }

    /**
     * Returns the ID of the sobject.
     *
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Type\ID $id
     */
    public function getId()
    {
        // "DUPLICATE ID FEATURE"
        if(is_array($this->Id))
        {
            $this->Id = $this->Id[0];
        }
        return $this->Id;
    }

    /**
     * Returns the sobject type.
     *
     *
     * @return string
     */
    public function getSobjectType()
    {
        return $this->type;
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
        $this->remove($offset);
    }

    /**
     *
     * @param scalar $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $this->convertAny();

        if(property_exists($this, $key))
        {
            return $this->$key;
        }
        return null;
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
        $this->_keyCache = null;

        $this->convertAny();

        $this->$key = $value;
    }

    /**
     * __call() magic.
     * Used to map getXY() and setXY() non-existent methods
     * to call get() and set().
     *
     *
     * @param $name
     * @param array $args
     *
     * @throws \BadMethodCallException
     * @return mixed.
     */
    public function __call($name, array $args = array())
    {
        throw new \BadMethodCallException(sprintf('Method "%s" does not exists.', $name));
    }

    /**
     *
     * @param array|MapInterface $values
     *
     * @return void
     */
    public function putAll($values)
    {
        foreach($values AS $key => $value)
        {
            $this->put($key, $value);
        }
    }

    /**
     * @param scalar $key
     *
     * @return bool
     */
    public function contains($key)
    {
        $this->convertAny();

        return property_exists($this, $key);
    }

    /**
     * @param scalar $key
     *
     * @return mixed
     */
    public function remove($key)
    {
        $this->_keyCache = null;

        $this->convertAny();

        $oldValue = $this->$key;

        unset($this->$key);

        return $oldValue;
    }

    /**
     * getValues(), used for iterating
     *
     * @see getIterator()
     * @return ListInterface
     */
    public function getValues()
    {
        $this->convertAny();

        return new SobjectPropertyIterator($this);
    }

    /**
     * toArray()
     *
     * @return array
     */
    public function toArray()
    {
        if(null === $this->_keyCache)
        {
            $this->getKeys();
        }

        return $this->_valueCache;
    }

    /**
     *
     * @return ListInterface
     */
    public function getKeys()
    {
        $this->convertAny();

        if(null === $this->_keyCache)
        {
            $this->_valueCache = array();

            $this->_keyCache = array();

            $reflObj = new \ReflectionObject($this);

            foreach($reflObj->getProperties(\ReflectionProperty::IS_PUBLIC) AS $reflProp)
            {
                if( ! $reflProp->isStatic())
                {
                    $key = $reflProp->getName();
                    $this->_keyCache[] = $key;
                    $this->_valueCache[$key] = $this->$key;
                }
            }
        }

        return $this->_keyCache;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->toArray());
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
        foreach(unserialize($serialized) AS $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * Special handling of <anyxml> type:
     * Converty any xml stream to sobject properties.
     *
     * @return void
     */
    private function convertAny()
    {
        if(property_exists($this, 'any'))
        {
            $hydrator = new ResultHydrator();

            foreach($hydrator->fromAny($this->any) AS $key => $value)
            {
                $this->$key = $value;
            }

            unset($this->any);
        }
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
        return count($this->getKeys());
    }

    /**
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Type\ID | string $id
     * @return void
     */
    public function setId($id)
    {
        $this->Id = $id;
    }
}
