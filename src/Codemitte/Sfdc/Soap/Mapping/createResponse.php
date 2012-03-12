<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class createResponse implements ClassInterface
{
    /**
     * @var SaveResult $result
     */
    private $result;

    /**
     * Constructor.
     *
     * @param SaveResult $result
     *
     * @access public
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * getResult()
     *
     * @return \SaveResult
     */
    public function getResult()
    {
        return $this->result;
    }
}
