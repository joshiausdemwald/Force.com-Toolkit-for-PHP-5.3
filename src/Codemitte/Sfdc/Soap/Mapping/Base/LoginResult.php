<?php
namespace Codemitte\Sfdc\Soap\Mapping\Base;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

/**
 * LoginResult
 */
class LoginResult implements ClassInterface
{
    /**
     * @var string
     */
    private $metadataServerUrl;

    /**
     * @var bool
     */
    private $passwordExpired;

    /**
     * @var bool
     */
    private $sandbox;

    /**
     * @var string
     */
    private $serverUrl;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var GetUserInfoResult
     */
    private $userInfo;

    /**
     * @return GetUserInfoResult
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * @return string
     */
    public function getMetadataServerUrl()
    {
        return $this->metadataServerUrl;
    }

    /**
     * @return boolean
     */
    public function getPasswordExpired()
    {
        return $this->passwordExpired;
    }

    /**
     * @return boolean
     */
    public function getSandbox()
    {
        return $this->sandbox;
    }

    /**
     * @return string
     */
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
