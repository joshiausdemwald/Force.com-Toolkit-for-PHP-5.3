<?php
namespace Codemitte\ForceToolkit\Soap\Mapping\Base;

use Codemitte\Soap\Mapping\ClassInterface;

class LoginScopeHeader implements ClassInterface
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
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Base\ID
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Base\ID
     */
    public function getPortalId()
    {
        return $this->portalId;
    }

}
