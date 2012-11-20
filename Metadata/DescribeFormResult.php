<?php
namespace Codemitte\ForceToolkit\Metadata;

use Codemitte\Common\Cache\GenericCachedInterface;

class DescribeFormResult implements DescribeFormResultInterface, GenericCachedInterface, \Serializable
{
    /**
     * @var timestamp
     */
    protected $createdAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = time();
    }

    /**
     * @var array<FieldInterface>
     */
    protected $fields = array();

    /**
     * @var array<string, FieldInterface>
     */
    protected $fieldIndex = array();

    /**
     * Returns all field as an (ordered) list.
     *
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Returns a specific field definition by name.
     *
     * @param $name
     * @throws \InvalidArgumentException
     * @return FieldInterface
     */
    public function getField($name)
    {
        if(! isset($this->fieldIndex[$name]))
        {
            throw new \InvalidArgumentException(sprintf('There is no field named "%s".', $name));
        }
        return $this->fieldIndex[$name];
    }

    /**
     * @param \Codemitte\ForceToolkit\Metadata\FieldInterface $field
     * @return mixed
     */
    public function addField(FieldInterface $field)
    {
        if( ! isset($this->fieldIndex[$field->getName()]))
        {
            $this->fields[] = $field;

            $this->fieldIndex[$field->getName()] = $field;
        }
    }

    /**
     * @param string/int $timestamp
     * @return bool
     */
    public function isFresh($timestamp)
    {
        return $this->createdAt >= $timestamp;
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
           'createdAt' => $this->createdAt,
           'fields' => $this->fields
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
        $unserialized = unserialize($serialized);

        $this->createdAt = $unserialized['createdAt'];

        foreach($unserialized['fields'] AS $field)
        {
            $this->addField($field);
        }
    }
}
