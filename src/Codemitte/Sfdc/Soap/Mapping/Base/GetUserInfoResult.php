<?php
namespace Codemitte\Sfdc\Soap\Mapping\Base;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

/**
 * UserInfo
 */
class GetUserInfoResult implements ClassInterface
{
    /**
     * @var bool
     */
    private $accessibilityMode;

    /**
     * @var string
     */
    private $currencySymbol;

    /**
     * @var int
     */
    private $orgAttachmentFileSizeLimit;

    /**
     * @var string
     */
    private $orgDefaultCurrencyIsoCode;

    /**
     * @var bool
     */
    private $orgDisallowHtmlAttachments;

    /**
     * @var bool
     */
    private $orgHasPersonAccounts;

    /**
     * @var
     */
    private $organizationId;

    /**
     * @var bool
     */
    private $organizationMultiCurrency;

    /**
     * @var string
     */
    private $organizationName;

    /**
     * @var string
     */
    private $profileId;

    /**
     * @var string
     */
    private $roleId;

    /**
     * @var int
     */
    private $sessionSecondsValid;

    /**
     * @var string
     */
    private $userDefaultCurrencyIsoCode;

    /**
     * @var string
     */
    private $userEmail;

    /**
     * @var string
     */
    private $userFullName;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $userLanguage;

    /**
     * @var string
     */
    private $userLocale;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $userTimeZone;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $userUiSkin;

    /**
     * @return boolean
     */
    public function getAccessibilityMode()
    {
        return $this->accessibilityMode;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * @return
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @return boolean
     */
    public function getOrganizationMultiCurrency()
    {
        return $this->organizationMultiCurrency;
    }

    /**
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * @return int
     */
    public function getOrgAttachmentFileSizeLimit()
    {
        return $this->orgAttachmentFileSizeLimit;
    }

    /**
     * @return string
     */
    public function getOrgDefaultCurrencyIsoCode()
    {
        return $this->orgDefaultCurrencyIsoCode;
    }

    /**
     * @return boolean
     */
    public function getOrgDisallowHtmlAttachments()
    {
        return $this->orgDisallowHtmlAttachments;
    }

    /**
     * @return boolean
     */
    public function getOrgHasPersonAccounts()
    {
        return $this->orgHasPersonAccounts;
    }

    /**
     * @return string
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @return int
     */
    public function getSessionSecondsValid()
    {
        return $this->sessionSecondsValid;
    }

    /**
     * @return string
     */
    public function getUserDefaultCurrencyIsoCode()
    {
        return $this->userDefaultCurrencyIsoCode;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @return string
     */
    public function getUserFullName()
    {
        return $this->userFullName;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserLanguage()
    {
        return $this->userLanguage;
    }

    /**
     * @return string
     */
    public function getUserLocale()
    {
        return $this->userLocale;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getUserTimeZone()
    {
        return $this->userTimeZone;
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @return string
     */
    public function getUserUiSkin()
    {
        return $this->userUiSkin;
    }
}
