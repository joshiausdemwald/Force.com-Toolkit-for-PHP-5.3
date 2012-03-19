<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class RecordType implements ClassInterface
{

    /**
     *
     * @var ID $BusinessProcessId
     */
    private $BusinessProcessId;

    /**
     *
     * @var User $CreatedBy
     */
    private $CreatedBy;

    /**
     *
     * @var ID $CreatedById
     */
    private $CreatedById;

    /**
     *
     * @var dateTime $CreatedDate
     */
    private $CreatedDate;

    /**
     *
     * @var string $Description
     */
    private $Description;

    /**
     *
     * @var string $DeveloperName
     */
    private $DeveloperName;

    /**
     *
     * @var boolean $IsActive
     */
    private $IsActive;

    /**
     *
     * @var boolean $IsPersonType
     */
    private $IsPersonType;

    /**
     *
     * @var User $LastModifiedBy
     */
    private $LastModifiedBy;

    /**
     *
     * @var ID $LastModifiedById
     */
    private $LastModifiedById;

    /**
     *
     * @var dateTime $LastModifiedDate
     */
    private $LastModifiedDate;

    /**
     *
     * @var QueryResult $Localization
     */
    private $Localization;

    /**
     *
     * @var string $Name
     */
    private $Name;

    /**
     *
     * @var string $NamespacePrefix
     */
    private $NamespacePrefix;

    /**
     *
     * @var string $SobjectType
     */
    private $SobjectType;

    /**
     *
     * @var dateTime $SystemModstamp
     */
    private $SystemModstamp;

    /**
     *
     * @param ID $BusinessProcessId
     * @param User $CreatedBy
     * @param ID $CreatedById
     * @param dateTime $CreatedDate
     * @param string $Description
     * @param string $DeveloperName
     * @param boolean $IsActive
     * @param boolean $IsPersonType
     * @param User $LastModifiedBy
     * @param ID $LastModifiedById
     * @param dateTime $LastModifiedDate
     * @param QueryResult $Localization
     * @param string $Name
     * @param string $NamespacePrefix
     * @param string $SobjectType
     * @param dateTime $SystemModstamp
     *
     * @access public
     */
    public function __construct(
        $BusinessProcessId, $CreatedBy, $CreatedById, $CreatedDate, $Description, $DeveloperName, $IsActive,
        $IsPersonType, $LastModifiedBy, $LastModifiedById, $LastModifiedDate, $Localization, $Name, $NamespacePrefix,
        $SobjectType, $SystemModstamp
    )
    {
        $this->BusinessProcessId = $BusinessProcessId;
        $this->CreatedBy         = $CreatedBy;
        $this->CreatedById       = $CreatedById;
        $this->CreatedDate       = $CreatedDate;
        $this->Description       = $Description;
        $this->DeveloperName     = $DeveloperName;
        $this->IsActive          = $IsActive;
        $this->IsPersonType      = $IsPersonType;
        $this->LastModifiedBy    = $LastModifiedBy;
        $this->LastModifiedById  = $LastModifiedById;
        $this->LastModifiedDate  = $LastModifiedDate;
        $this->Localization      = $Localization;
        $this->Name              = $Name;
        $this->NamespacePrefix   = $NamespacePrefix;
        $this->SobjectType       = $SobjectType;
        $this->SystemModstamp    = $SystemModstamp;
    }

}
