<?php
namespace Codemitte\Sfdc\Soap\Header;

use \SoapHeader;
use Codemitte\Sfdc\Soap\Mapping\Base\SessionHeader AS SessionData;

/**
 * SoapHeader
 */
class SessionHeader extends SoapHeader
{
    const HEADER_NAME = 'SessionHeader';

    /**
     * @var String
     */
    private $sessionId;

    /**
     * Constructor.
     *
     * @param string $namespace
     * @param string $sessionId
     */
    public function __construct($namespace, $sessionId)
    {
        parent::__construct($namespace, self::HEADER_NAME, new SessionData($sessionId), true);

        $this->sessionId = $sessionId;
    }

    /**
     * @return String
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
}
