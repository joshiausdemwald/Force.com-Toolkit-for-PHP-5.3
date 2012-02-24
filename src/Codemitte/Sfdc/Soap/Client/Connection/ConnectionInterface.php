<?php
namespace Codemitte\Sfdc\Soap\Client\Connection;

use \Serializable;

/**
 * ConnectionInterface
 */
interface ConnectionInterface extends Serializable
{
    /**
     * getWsdl()
     *
     * @abstract
     * @return string
     */
    public function getWsdl();

    /**
     * setWsdl
     *
     * @abstract
     * @param string $wsdl
     */
    public function setWsdl($wsdl);

    /**
     * Adds classes to the connection's soap classmap.
     *
     * @abstract
     *
     * @param string $type
     * @param string $classname
     */
    public function registerClass($type, $classname);

    /**
     * Adds types to the connection's soap typemap.
     *
     * @param string $typename
     * @param string $class
     * @param string $targetNamespace
     */
    public function registerType($typename, $class, $targetNamespace = null);

    /**
     * setOption()
     *
     * @abstract
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value);

    /**
     * getOption()
     *
     * @abstract
     * @param string $key
     * @return mixed
     */
    public function getOption($key);

    /**
     * getOptions()
     *
     * @abstract
     * @return array
     */
    public function getOptions();

    /**
     * setOptions()
     *
     * @abstract
     * @param array $options
     */
    public function setOptions(array $options);
}
