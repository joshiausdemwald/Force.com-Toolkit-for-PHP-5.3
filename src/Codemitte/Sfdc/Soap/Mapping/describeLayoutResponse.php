<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

/**
 * describeLayoutResponse
 */
class describeLayoutResponse implements ClassInterface
{
    /**
     * @var DescribeLayoutResult $result
     */
    private $result;

    /**
     *
     * @param DescribeLayoutResult $result
     *
     * @access public
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return DescribeLayoutResult
     */
    public function getResult()
    {
        return $this->result;
    }
}
