<?php
namespace Codemitte\ForceToolkit\Metadata;

use
    Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult,
    Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutResult,
    Codemitte\ForceToolkit\Soap\Mapping\Type\layoutComponentType,
    Codemitte\ForceToolkit\Soap\Mapping\Type\fieldType,
    Codemitte\Common\Cache\GenericCacheInterface,
    Codemitte\ForceToolkit\Metadata\Cache\MetadataCacheInterface
;

class DescribeFormFactory implements DescribeFormFactoryInterface
{
    /**
     * @var DescribeSobjectFactoryInterface
     */
    protected $describeSobjectFactory;

    /**
     * @var DescribeLayoutFactoryInterface
     */
    protected $describeLayoutFactory;

    /**
     * @var GenericCacheInterface $cache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param DescribeSobjectFactoryInterface $describeSobjectFactory
     * @param DescribeLayoutFactoryInterface $describeLayoutFactory
     * @param Cache\MetadataCacheInterface $cache
     */
    public function __construct(DescribeSobjectFactoryInterface $describeSobjectFactory, DescribeLayoutFactoryInterface $describeLayoutFactory, MetadataCacheInterface $cache)
    {
        $this->describeSobjectFactory   = $describeSobjectFactory;

        $this->describeLayoutFactory    = $describeLayoutFactory;

        $this->cache = $cache;
    }

    /**
     * Aggregates a describe sobject result and describe layout result into
     * a more valuable representation as an underlying datastructures utilized
     * to build a form.
     *
     * If RecordTypeId is given, the matching RT setting is taken, otherwise
     * the default record type settings.
     *
     * PicklistForRecordType: Represents a single record type picklist in a RecordTypeMapping. The picklistName matches
     * up with the name attribute of each field in the fields array in describeSObjectResult. The picklistValues are the
     * set of acceptable values for the recordType.
     *
     * Note: If you retrieve picklistValues, the validFor value is null. If you need the validFor value, get it from the
     * PicklistEntries object obtained from the Field object associated with the DescribeSObjectResult.
     *
     * DEPENDENT PICKLISTs @see http://jaswinderjohal.com/salesforce-dependent-list-php/
     *
     * @abstract
     * @param $sobjectType
     * @param string|\Codemitte\ForceToolkit\Soap\Mapping\Type\ID|null $recordTypeId
     * @return DescribeFormResult
     */
    public function getDescribe($sobjectType, $recordTypeId = null)
    {
        $cache_key = $sobjectType;

        if($recordTypeId)
        {
            $cache_key .= $recordTypeId;
        }

        $cache_key .= '.describeFormResult';

        if($this->cache->has($cache_key))
        {
            if($this->cache->isFresh($cache_key))
            {
                return $this->cache->get($cache_key);
            }
            $this->cache->remove($cache_key);
        }

        if(null !== $recordTypeId)
        {
            $recordTypeId = (string)$recordTypeId;
        }

        $describeSobjectResult   = $this->describeSobjectFactory->getDescribe($sobjectType);

        $describeLayoutResult    = $this->describeLayoutFactory->getDescribe($sobjectType, null === $recordTypeId ? null : array($recordTypeId));

        $recordTypeMappings = $describeLayoutResult->getRecordTypeMappings();

        $result = new DescribeFormResult();

        // FALLBACK 1: FIRST RESULT
        /** @var $recordTypeMapping \Codemitte\ForceToolkit\Soap\Mapping\RecordTypeMapping */
        $recordTypeMapping = count($recordTypeMappings) > 0 ? $recordTypeMappings[0] : null;

        // FALLBACK 2: DEFAULT RT MAPPING
        /** @var $rtm \Codemitte\ForceToolkit\Soap\Mapping\RecordTypeMapping */
        foreach($recordTypeMappings AS $rtm)
        {
            if($rtm->getDefaultRecordTypeMapping())
            {
                $recordTypeMapping = $rtm;
                break;
            }
        }

        // FALLBACK 3: MATCHING RT MAPPING
        if(null !== $recordTypeId)
        {
            /** @var $rtm \Codemitte\ForceToolkit\Soap\Mapping\RecordTypeMapping */
            foreach($recordTypeMappings AS $rtm)
            {
                if((string)$rtm->getRecordTypeId() === $recordTypeId)
                {
                    $recordTypeMapping = $rtm;
                    break;
                }
            }
        } // --- END DETERMINATION OF RECORD TYPE MAPPING

        // EVALUATION OF PAGE LAYOUT INFORMATION, E.G.
        // MAP OF isRequired FIELDNAMES

        $fieldLabels    = array();
        $requiredFields = array();
        $readonlyFields = array();

        /** @var $layout \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayout */
        foreach($describeLayoutResult->getLayouts() AS $layout)
        {
            // FOUND LAYOUT!
            if((string)$layout->getId() === (string)$recordTypeMapping->getLayoutId())
            {
                /** @var $editLayoutSection \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutSection */
                foreach($layout->getEditLayoutSections() AS $editLayoutSection)
                {
                    /** @var $layoutRow \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutRow */
                    foreach($editLayoutSection->getLayoutRows() AS $layoutRow)
                    {
                        /** @var $layoutItem \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutItem */
                        foreach($layoutRow->getLayoutItems() AS $layoutItem)
                        {
                            if($layoutItem->getLayoutComponents())
                            {
                                /** @var $component \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutComponent */
                                foreach($layoutItem->getLayoutComponents() AS $component)
                                {
                                    // FOUND FIELD
                                    if((string)$component->getType() === layoutComponentType::Field)
                                    {
                                        $fieldLabels[$component->getValue()] = $layoutItem->getLabel();

                                        if( ! $layoutItem->getEditable())
                                        {
                                            $readonlyFields[] = $component->getValue();
                                        }

                                        if($layoutItem->getRequired())
                                        {
                                            $requiredFields[] = $component->getValue();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            }
        } // -- END EVALUATION OF PAGE LAYOUT RELATED INFORMATION

        // SORT "ORIGINAL" FIELD METADATA BY FIELD NAMES
        $fieldsByName = array();

        foreach($describeSobjectResult->getFields() AS $field)
        {
            $fieldsByName[$field->getName()] = $field;
        }

        /* @var $picklistForRecordType \Codemitte\ForceToolkit\Soap\Mapping\PicklistForRecordType */
        foreach($recordTypeMapping->getPicklistsForRecordType() AS $picklistForRecordType)
        {
            /** @var $originalField \Codemitte\ForceToolkit\Soap\Mapping\Field */
            $originalField = $fieldsByName[$picklistForRecordType->getPicklistName()];

            $originalPicklistEntries = array();

            /** @var $originalPicklistEntry \Codemitte\ForceToolkit\Soap\Mapping\PicklistEntry */
            foreach($originalField->getPicklistValues() AS $originalPicklistEntry)
            {
                $originalPicklistEntries[$originalPicklistEntry->getValue()] = $originalPicklistEntry;
            }

            $picklistEntries = array();

            /** @var $picklistEntry \Codemitte\ForceToolkit\Soap\Mapping\PicklistEntry */
            foreach($picklistForRecordType->getPicklistValues() AS $picklistEntry)
            {
                /** @var $originalPicklistEntry \Codemitte\ForceToolkit\Soap\Mapping\PicklistEntry */
                $originalPicklistEntry = $originalPicklistEntries[$picklistEntry->getValue()];

                if($originalPicklistEntry->getActive())
                {
                    $picklistEntries[] = $originalPicklistEntry;
                }
            }

            $fieldname = $originalField->getName();

            $result->addField(new Field(
                $sobjectType,
                $fieldname,
                $originalField->getType(),
                isset($fieldLabels[$fieldname]) ? $fieldLabels[$fieldname] : $originalField->getLabel(),
                in_array($fieldname, $requiredFields) ? true :  ! $field->getNillable(),
                in_array($fieldname, $readonlyFields) ? true : false,
                $originalField,
                $picklistEntries
            ));
        }

        /** The rest of the fields */
        /** @var $field \Codemitte\ForceToolkit\Soap\Mapping\Field */
        foreach($describeSobjectResult->getFields() AS $field)
        {
            $fieldname = $field->getName();

            $result->addField(new Field(
                $sobjectType,
                $fieldname,
                $field->getType(),
                isset($fieldLabels[$fieldname]) ? $fieldLabels[$fieldname] : $field->getLabel(),
                in_array($fieldname, $requiredFields) ? true : ! $field->getNillable(),
                in_array($fieldname, $readonlyFields) ? true : false,
                $field
            ));
        }

        $this->cache->set($cache_key, $result);

        return $result;
    }
}