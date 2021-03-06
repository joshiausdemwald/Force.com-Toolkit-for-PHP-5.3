<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class Field implements ClassInterface, \Serializable
{
    /**
     *
     * @var boolean $autoNumber
     */
    private $autoNumber;

    /**
     *
     * @var int $byteLength
     */
    private $byteLength;

    /**
     *
     * @var boolean $calculated
     */
    private $calculated;

    /**
     *
     * @var string $calculatedFormula
     */
    private $calculatedFormula;

    /**
     *
     * @var boolean $caseSensitive
     */
    private $caseSensitive;

    /**
     *
     * @var string $controllerName
     */
    private $controllerName;

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
     * @var string $defaultValueFormula
     */
    private $defaultValueFormula;

    /**
     *
     * @var boolean $defaultedOnCreate
     */
    private $defaultedOnCreate;

    /**
     *
     * @var boolean $dependentPicklist
     */
    private $dependentPicklist;

    /**
     *
     * @var boolean $deprecatedAndHidden
     */
    private $deprecatedAndHidden;

    /**
     *
     * @var int $digits
     */
    private $digits;

    /**
     *
     * @var boolean $externalId
     */
    private $externalId;

    /**
     *
     * @var boolean $filterable
     */
    private $filterable;

    /**
     *
     * @var boolean $groupable
     */
    private $groupable;

    /**
     *
     * @var boolean $htmlFormatted
     */
    private $htmlFormatted;

    /**
     *
     * @var boolean $idLookup
     */
    private $idLookup;

    /**
     *
     * @var string $inlineHelpText
     */
    private $inlineHelpText;

    /**
     *
     * @var string $label
     */
    private $label;

    /**
     *
     * @var int $length
     */
    private $length;

    /**
     *
     * @var string $name
     */
    private $name;

    /**
     *
     * @var boolean $nameField
     */
    private $nameField;

    /**
     *
     * @var boolean $namePointing
     */
    private $namePointing;

    /**
     *
     * @var boolean $nillable
     */
    private $nillable;

    /**
     *
     * @var boolean $permissionable
     */
    private $permissionable;

    /**
     *
     * @var PicklistEntry $picklistValues
     */
    private $picklistValues;

    /**
     *
     * @var int $precision
     */
    private $precision;

    /**
     *
     * @var string $referenceTo
     */
    private $referenceTo;

    /**
     *
     * @var string $relationshipName
     */
    private $relationshipName;

    /**
     *
     * @var int $relationshipOrder
     */
    private $relationshipOrder;

    /**
     *
     * @var boolean $restrictedPicklist
     */
    private $restrictedPicklist;

    /**
     *
     * @var int $scale
     */
    private $scale;

    /**
     *
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Type\soapType $soapType
     */
    private $soapType;

    /**
     *
     * @var boolean $sortable
     */
    private $sortable;

    /**
     *
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Type\fieldType $type
     */
    private $type;

    /**
     *
     * @var boolean $unique
     */
    private $unique;

    /**
     *
     * @var boolean $updateable
     */
    private $updateable;

    /**
     *
     * @var boolean $writeRequiresMasterRead
     */
    private $writeRequiresMasterRead;

    /**
     *
     * @param boolean $autoNumber
     * @param int $byteLength
     * @param boolean $calculated
     * @param string $calculatedFormula
     * @param boolean $caseSensitive
     * @param string $controllerName
     * @param boolean $createable
     * @param boolean $custom
     * @param string $defaultValueFormula
     * @param boolean $defaultedOnCreate
     * @param boolean $dependentPicklist
     * @param boolean $deprecatedAndHidden
     * @param int $digits
     * @param boolean $externalId
     * @param boolean $filterable
     * @param boolean $groupable
     * @param boolean $htmlFormatted
     * @param boolean $idLookup
     * @param string $inlineHelpText
     * @param string $label
     * @param int $length
     * @param string $name
     * @param boolean $nameField
     * @param boolean $namePointing
     * @param boolean $nillable
     * @param boolean $permissionable
     * @param PicklistEntry $picklistValues
     * @param int $precision
     * @param string $referenceTo
     * @param string $relationshipName
     * @param int $relationshipOrder
     * @param boolean $restrictedPicklist
     * @param int $scale
     * @param soapType $soapType
     * @param boolean $sortable
     * @param fieldType $type
     * @param boolean $unique
     * @param boolean $updateable
     * @param boolean $writeRequiresMasterRead
     *
     * @access public
     */
    public function __construct(
        $autoNumber, $byteLength, $calculated, $calculatedFormula, $caseSensitive, $controllerName, $createable,
        $custom, $defaultValueFormula, $defaultedOnCreate, $dependentPicklist, $deprecatedAndHidden, $digits,
        $externalId, $filterable, $groupable, $htmlFormatted, $idLookup, $inlineHelpText, $label, $length, $name,
        $nameField, $namePointing, $nillable, $permissionable, $picklistValues, $precision, $referenceTo,
        $relationshipName, $relationshipOrder, $restrictedPicklist, $scale, $soapType, $sortable, $type, $unique,
        $updateable, $writeRequiresMasterRead
    )
    {
        $this->autoNumber              = $autoNumber;
        $this->byteLength              = $byteLength;
        $this->calculated              = $calculated;
        $this->calculatedFormula       = $calculatedFormula;
        $this->caseSensitive           = $caseSensitive;
        $this->controllerName          = $controllerName;
        $this->createable              = $createable;
        $this->custom                  = $custom;
        $this->defaultValueFormula     = $defaultValueFormula;
        $this->defaultedOnCreate       = $defaultedOnCreate;
        $this->dependentPicklist       = $dependentPicklist;
        $this->deprecatedAndHidden     = $deprecatedAndHidden;
        $this->digits                  = $digits;
        $this->externalId              = $externalId;
        $this->filterable              = $filterable;
        $this->groupable               = $groupable;
        $this->htmlFormatted           = $htmlFormatted;
        $this->idLookup                = $idLookup;
        $this->inlineHelpText          = $inlineHelpText;
        $this->label                   = $label;
        $this->length                  = $length;
        $this->name                    = $name;
        $this->nameField               = $nameField;
        $this->namePointing            = $namePointing;
        $this->nillable                = $nillable;
        $this->permissionable          = $permissionable;
        $this->picklistValues          = $picklistValues;
        $this->precision               = $precision;
        $this->referenceTo             = $referenceTo;
        $this->relationshipName        = $relationshipName;
        $this->relationshipOrder       = $relationshipOrder;
        $this->restrictedPicklist      = $restrictedPicklist;
        $this->scale                   = $scale;
        $this->soapType                = $soapType;
        $this->sortable                = $sortable;
        $this->type                    = $type;
        $this->unique                  = $unique;
        $this->updateable              = $updateable;
        $this->writeRequiresMasterRead = $writeRequiresMasterRead;
    }

    /**
     * @return boolean
     */
    public function getAutoNumber()
    {
        return $this->autoNumber;
    }

    /**
     * @return int
     */
    public function getByteLength()
    {
        return $this->byteLength;
    }

    /**
     * @return boolean
     */
    public function getCalculated()
    {
        return $this->calculated;
    }

    /**
     * @return string
     */
    public function getCalculatedFormula()
    {
        return $this->calculatedFormula;
    }

    /**
     * @return boolean
     */
    public function getCaseSensitive()
    {
        return $this->caseSensitive;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
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
     * @return string
     */
    public function getDefaultValueFormula()
    {
        return $this->defaultValueFormula;
    }

    /**
     * @return boolean
     */
    public function getDefaultedOnCreate()
    {
        return $this->defaultedOnCreate;
    }

    /**
     * @return boolean
     */
    public function getDependentPicklist()
    {
        return $this->dependentPicklist;
    }

    /**
     * @return boolean
     */
    public function getDeprecatedAndHidden()
    {
        return $this->deprecatedAndHidden;
    }

    /**
     * @return int
     */
    public function getDigits()
    {
        return $this->digits;
    }

    /**
     * @return boolean
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @return boolean
     */
    public function getFilterable()
    {
        return $this->filterable;
    }

    /**
     * @return boolean
     */
    public function getGroupable()
    {
        return $this->groupable;
    }

    /**
     * @return boolean
     */
    public function getHtmlFormatted()
    {
        return $this->htmlFormatted;
    }

    /**
     * @return boolean
     */
    public function getIdLookup()
    {
        return $this->idLookup;
    }

    /**
     * @return string
     */
    public function getInlineHelpText()
    {
        return $this->inlineHelpText;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
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
    public function getNameField()
    {
        return $this->nameField;
    }

    /**
     * @return boolean
     */
    public function getNamePointing()
    {
        return $this->namePointing;
    }

    /**
     * @return boolean
     */
    public function getNillable()
    {
        return $this->nillable;
    }

    /**
     * @return boolean
     */
    public function getPermissionable()
    {
        return $this->permissionable;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\PicklistEntry
     */
    public function getPicklistValues()
    {
        return $this->picklistValues;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return string
     */
    public function getReferenceTo()
    {
        return $this->referenceTo;
    }

    /**
     * @return string
     */
    public function getRelationshipName()
    {
        return $this->relationshipName;
    }

    /**
     * @return int
     */
    public function getRelationshipOrder()
    {
        return $this->relationshipOrder;
    }

    /**
     * @return boolean
     */
    public function getRestrictedPicklist()
    {
        return $this->restrictedPicklist;
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\soapType
     */
    public function getSoapType()
    {
        return $this->soapType;
    }

    /**
     * @return boolean
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\fieldType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * @return boolean
     */
    public function getUpdateable()
    {
        return $this->updateable;
    }

    /**
     * @return boolean
     */
    public function getWriteRequiresMasterRead()
    {
        return $this->writeRequiresMasterRead;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            'autoNumber'              =>  $this->autoNumber,
            'byteLength'              =>  $this->byteLength,
            'calculated'              =>  $this->calculated,
            'calculatedFormula'       =>  $this->calculatedFormula,
            'caseSensitive'           =>  $this->caseSensitive,
            'controllerName'          =>  $this->controllerName,
            'createable'              =>  $this->createable,
            'custom'                  =>  $this->custom,
            'defaultValueFormula'     =>  $this->defaultValueFormula,
            'defaultedOnCreate'       =>  $this->defaultedOnCreate,
            'dependentPicklist'       =>  $this->dependentPicklist,
            'deprecatedAndHidden'     =>  $this->deprecatedAndHidden,
            'digits'                  =>  $this->digits,
            'externalId'              =>  $this->externalId,
            'filterable'              =>  $this->filterable,
            'groupable'               =>  $this->groupable,
            'htmlFormatted'           =>  $this->htmlFormatted,
            'idLookup'                =>  $this->idLookup,
            'inlineHelpText'          =>  $this->inlineHelpText,
            'label'                   =>  $this->label,
            'length'                  =>  $this->length,
            'name'                    =>  $this->name,
            'nameField'               =>  $this->nameField,
            'namePointing'            =>  $this->namePointing,
            'nillable'                =>  $this->nillable,
            'permissionable'          =>  $this->permissionable,
            'picklistValues'          =>  $this->picklistValues,
            'precision'               =>  $this->precision,
            'referenceTo'             =>  $this->referenceTo,
            'relationshipName'        =>  $this->relationshipName,
            'relationshipOrder'       =>  $this->relationshipOrder,
            'restrictedPicklist'      =>  $this->restrictedPicklist,
            'scale'                   =>  $this->scale,
            'soapType'                =>  $this->soapType,
            'sortable'                =>  $this->sortable,
            'type'                    =>  $this->type,
            'unique'                  =>  $this->unique,
            'updateable'              =>  $this->updateable,
            'writeRequiresMasterRead' =>  $this->writeRequiresMasterRead
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        
        $this->autoNumber              =  $data['autoNumber'];            
        $this->byteLength              =  $data['byteLength'];            
        $this->calculated              =  $data['calculated'];            
        $this->calculatedFormula       =  $data['calculatedFormula'];     
        $this->caseSensitive           =  $data['caseSensitive'];         
        $this->controllerName          =  $data['controllerName'];        
        $this->createable              =  $data['createable'];            
        $this->custom                  =  $data['custom'];                
        $this->defaultValueFormula     =  $data['defaultValueFormula'];   
        $this->defaultedOnCreate       =  $data['defaultedOnCreate'];     
        $this->dependentPicklist       =  $data['dependentPicklist'];     
        $this->deprecatedAndHidden     =  $data['deprecatedAndHidden'];   
        $this->digits                  =  $data['digits'];                
        $this->externalId              =  $data['externalId'];            
        $this->filterable              =  $data['filterable'];            
        $this->groupable               =  $data['groupable'];             
        $this->htmlFormatted           =  $data['htmlFormatted'];         
        $this->idLookup                =  $data['idLookup'];              
        $this->inlineHelpText          =  $data['inlineHelpText'];        
        $this->label                   =  $data['label'];                 
        $this->length                  =  $data['length'];                
        $this->name                    =  $data['name'];                  
        $this->nameField               =  $data['nameField'];             
        $this->namePointing            =  $data['namePointing'];          
        $this->nillable                =  $data['nillable'];              
        $this->permissionable          =  $data['permissionable'];        
        $this->picklistValues          =  $data['picklistValues'];        
        $this->precision               =  $data['precision'];             
        $this->referenceTo             =  $data['referenceTo'];           
        $this->relationshipName        =  $data['relationshipName'];      
        $this->relationshipOrder       =  $data['relationshipOrder'];     
        $this->restrictedPicklist      =  $data['restrictedPicklist'];    
        $this->scale                   =  $data['scale'];                 
        $this->soapType                =  $data['soapType'];              
        $this->sortable                =  $data['sortable'];              
        $this->type                    =  $data['type'];                  
        $this->unique                  =  $data['unique'];                
        $this->updateable              =  $data['updateable'];            
        $this->writeRequiresMasterRead =  $data['writeRequiresMasterRead'];
    }
}
