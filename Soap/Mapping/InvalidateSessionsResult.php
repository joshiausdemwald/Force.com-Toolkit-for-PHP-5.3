<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class InvalidateSessionsResult implements ClassInterface
{
    /**
     * @var boolean
     */
    private $success;

    /**
     * @var array|null
     */
    private $errors;

    /**
     * Errors: An array of Error objects.
     * <complexType name="Error">
     * <sequence>
     * <element name="fields"     type="xsd:string" nillable="true" minOccurs="0" maxOccurs="unbounded"/>
     * <element name="message"    type="xsd:string"/>
     *  <element name="statusCode" type="tns:StatusCode"/>
     * </sequence>
     * </complexType>
     *
     * @param $success
     * @param array $errors
     */
    public function __construct($success, array $errors = null)
    {
        $this->success = $success;

        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccess()
    {
        return $this->success;
    }
}
