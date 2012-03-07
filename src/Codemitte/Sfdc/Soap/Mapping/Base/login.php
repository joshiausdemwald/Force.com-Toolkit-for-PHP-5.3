<?php
namespace Codemitte\Sfdc\Soap\Mapping\Base;

use Codemitte\Soap\Mapping\ClassInterface;

class login implements ClassInterface
{
    /**
     * @var string $username
     * @access private
     */
    private $username;

    /**
     * @var string $password
     * @access private
     */
    private $password;

    /**
     * Constructor.
     *
     * @param string $username
     * @param string $password
     *
     * @access public
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

}
