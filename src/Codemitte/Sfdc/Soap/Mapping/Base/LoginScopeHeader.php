<?php
namespace Codemitte\Sfdc\Soap\Mapping\Base;

class LoginScopeHeader
{
    /**
     *
     * @var ID $organizationId
     */
    private $organizationId;

    /**
     *
     * @var ID $portalId
     */
    private $portalId;

    /**
     *
     * @param ID $organizationId
     * @param ID $portalId
     *
     * @access public
     */
    public function __construct($organizationId, $portalId)
    {
        $this->organizationId = $organizationId;
        $this->portalId       = $portalId;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\Base\ID
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\Base\ID
     */
    public function getPortalId()
    {
        return $this->portalId;
    }

}
