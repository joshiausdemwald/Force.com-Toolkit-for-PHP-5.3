<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class DescribeSObjectResult implements ClassInterface
{
    /**
     *
     * @var boolean $activateable
     */
    private $activateable;

    /**
     *
     * @var ChildRelationship $childRelationships
     */
    private $childRelationships;

    /**
     *
     * @var boolean $createable
     */
    private $createable;

    /**
     *
     * @var boolean $custom
     */
    private $custom;

    /**
     *
     * @var boolean $customSetting
     */
    private $customSetting;

    /**
     *
     * @var boolean $deletable
     */
    private $deletable;

    /**
     *
     * @var boolean $deprecatedAndHidden
     */
    private $deprecatedAndHidden;

    /**
     *
     * @var boolean $feedEnabled
     */
    private $feedEnabled;

    /**
     *
     * @var Field $fields
     */
    private $fields;

    /**
     *
     * @var string $keyPrefix
     */
    private $keyPrefix;

    /**
     *
     * @var string $label
     */
    private $label;

    /**
     *
     * @var string $labelPlural
     */
    private $labelPlural;

    /**
     *
     * @var boolean $layoutable
     */
    private $layoutable;

    /**
     *
     * @var boolean $mergeable
     */
    private $mergeable;

    /**
     *
     * @var string $name
     */
    private $name;

    /**
     *
     * @var boolean $queryable
     */
    private $queryable;

    /**
     *
     * @var RecordTypeInfo $recordTypeInfos
     */
    private $recordTypeInfos;

    /**
     *
     * @var boolean $replicateable
     */
    private $replicateable;

    /**
     *
     * @var boolean $retrieveable
     */
    private $retrieveable;

    /**
     *
     * @var boolean $searchable
     */
    private $searchable;

    /**
     *
     * @var boolean $triggerable
     */
    private $triggerable;

    /**
     *
     * @var boolean $undeletable
     */
    private $undeletable;

    /**
     *
     * @var boolean $updateable
     */
    private $updateable;

    /**
     *
     * @var string $urlDetail
     */
    private $urlDetail;

    /**
     *
     * @var string $urlEdit
     */
    private $urlEdit;

    /**
     *
     * @var string $urlNew
     */
    private $urlNew;

    /**
     *
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Type\soapType
     */
    private $soapType;

    /**
     *
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Type\fieldType
     */
    private $type;

    /**
     *
     * @param boolean $activateable
     * @param ChildRelationship $childRelationships
     * @param boolean $createable
     * @param boolean $custom
     * @param boolean $customSetting
     * @param boolean $deletable
     * @param boolean $deprecatedAndHidden
     * @param boolean $feedEnabled
     * @param Field $fields
     * @param string $keyPrefix
     * @param string $label
     * @param string $labelPlural
     * @param boolean $layoutable
     * @param boolean $mergeable
     * @param string $name
     * @param boolean $queryable
     * @param RecordTypeInfo $recordTypeInfos
     * @param boolean $replicateable
     * @param boolean $retrieveable
     * @param boolean $searchable
     * @param boolean $triggerable
     * @param boolean $undeletable
     * @param boolean $updateable
     * @param string $urlDetail
     * @param string $urlEdit
     * @param string $urlNew
     *
     * @access public
     */
    public function __construct(
        $activateable, $childRelationships, $createable, $custom, $customSetting, $deletable, $deprecatedAndHidden,
        $feedEnabled, $fields, $keyPrefix, $label, $labelPlural, $layoutable, $mergeable, $name, $queryable,
        $recordTypeInfos, $replicateable, $retrieveable, $searchable, $triggerable, $undeletable, $updateable,
        $urlDetail, $urlEdit, $urlNew
    )
    {
        $this->activateable        = $activateable;
        $this->childRelationships  = $childRelationships;
        $this->createable          = $createable;
        $this->custom              = $custom;
        $this->customSetting       = $customSetting;
        $this->deletable           = $deletable;
        $this->deprecatedAndHidden = $deprecatedAndHidden;
        $this->feedEnabled         = $feedEnabled;
        $this->fields              = $fields;
        $this->keyPrefix           = $keyPrefix;
        $this->label               = $label;
        $this->labelPlural         = $labelPlural;
        $this->layoutable          = $layoutable;
        $this->mergeable           = $mergeable;
        $this->name                = $name;
        $this->queryable           = $queryable;
        $this->recordTypeInfos     = $recordTypeInfos;
        $this->replicateable       = $replicateable;
        $this->retrieveable        = $retrieveable;
        $this->searchable          = $searchable;
        $this->triggerable         = $triggerable;
        $this->undeletable         = $undeletable;
        $this->updateable          = $updateable;
        $this->urlDetail           = $urlDetail;
        $this->urlEdit             = $urlEdit;
        $this->urlNew              = $urlNew;
    }

    /**
     * @return boolean
     */
    public function getActivateable()
    {
        return $this->activateable;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\ChildRelationship
     */
    public function getChildRelationships()
    {
        return $this->childRelationships;
    }

    /**
     * @return boolean
     */
    public function getCreateable()
    {
        return $this->createable;
    }

    /**
     * @return boolean
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @return boolean
     */
    public function getCustomSetting()
    {
        return $this->customSetting;
    }

    /**
     * @return boolean
     */
    public function getDeletable()
    {
        return $this->deletable;
    }

    /**
     * @return boolean
     */
    public function getDeprecatedAndHidden()
    {
        return $this->deprecatedAndHidden;
    }

    /**
     * @return boolean
     */
    public function getFeedEnabled()
    {
        return $this->feedEnabled;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Field
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getKeyPrefix()
    {
        return $this->keyPrefix;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getLabelPlural()
    {
        return $this->labelPlural;
    }

    /**
     * @return boolean
     */
    public function getLayoutable()
    {
        return $this->layoutable;
    }

    /**
     * @return boolean
     */
    public function getMergeable()
    {
        return $this->mergeable;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function getQueryable()
    {
        return $this->queryable;
    }

    /**
     * @return array<\Codemitte\ForceToolkit\Soap\Mapping\RecordTypeInfo>
     */
    public function getRecordTypeInfos()
    {
        return $this->recordTypeInfos;
    }

    /**
     * @return boolean
     */
    public function getReplicateable()
    {
        return $this->replicateable;
    }

    /**
     * @return boolean
     */
    public function getRetrieveable()
    {
        return $this->retrieveable;
    }

    /**
     * @return boolean
     */
    public function getSearchable()
    {
        return $this->searchable;
    }

    /**
     * @return boolean
     */
    public function getTriggerable()
    {
        return $this->triggerable;
    }

    /**
     * @return boolean
     */
    public function getUndeletable()
    {
        return $this->undeletable;
    }

    /**
     * @return boolean
     */
    public function getUpdateable()
    {
        return $this->updateable;
    }

    /**
     * @return string
     */
    public function getUrlDetail()
    {
        return $this->urlDetail;
    }

    /**
     * @return string
     */
    public function getUrlEdit()
    {
        return $this->urlEdit;
    }

    /**
     * @return string
     */
    public function getUrlNew()
    {
        return $this->urlNew;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Type\fieldType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Type\soapType
     */
    public function getSoapType()
    {
        return $this->soapType;
    }
}
