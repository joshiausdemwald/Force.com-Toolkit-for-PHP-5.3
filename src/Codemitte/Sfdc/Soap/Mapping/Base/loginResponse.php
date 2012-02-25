<?php
namespace Codemitte\Sfdc\Soap\Mapping\Base;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

class loginResponse implements ClassInterface
{
    /**
     *
     * @var LoginResult $result
     * @access private
     */
    private $result;

    /**
     *
     * @param LoginResult $result
     *
     * @access public
     */
    public function __construct(LoginResult $result)
    {
        $this->result = $result;
    }

    /**
     * @return LoginResult
     */
    public function getResult()
    {
        return $this->result;
    }

}
