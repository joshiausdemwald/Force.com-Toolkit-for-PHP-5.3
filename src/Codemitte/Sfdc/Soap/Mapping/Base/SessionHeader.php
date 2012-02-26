<?php
namespace Codemitte\Sfdc\Soap\Mapping\Base;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

class SessionHeader implements ClassInterface
{
    /**
     * @var string $sessionId
     *
     * @access private
     */
    private $sessionId;

    /**
     * @param string $sessionId
     *
     * @access public
     */
    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

}
