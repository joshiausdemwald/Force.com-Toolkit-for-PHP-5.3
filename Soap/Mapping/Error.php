<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use
    Codemitte\Soap\Mapping\ClassInterface,
    Codemitte\ForceToolkit\Soap\Mapping\Type\StatusCode
;

/**
 * <complexType name="Error">
 * <sequence>
 * <element name="fields"     type="xsd:string" nillable="true" minOccurs="0" maxOccurs="unbounded"/>
 * <element name="message"    type="xsd:string"/>
 * <element name="statusCode" type="tns:StatusCode"/>
 * </sequence>
 * </complexType>
 */
class Error implements ClassInterface
{
    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var string
     */
    private $message;

    /**
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Type\StatusCode
     */
    private $statusCode;

    /**
     * @param $fields
     * @param $message
     * @param $statusCode
     */
    public function __construct(array $fields = array(), $message = null, StatusCode $statusCode = null)
    {
        $this->fields = $fields;

        $this->message = $message;

        $this->statusCode = $statusCode;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }


}
