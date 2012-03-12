<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\GenericResult;

class sObject extends GenericResult
{
    /**
     * @var ID $Id
     *
     * @access public
     */
    private $Id;

    /**
     * @var string
     */
    private $sobjectType;

    /**
     * Constructor.
     *
     * @param string $sobjectType: The sobject type. If null, it will be determined out of the called classname.
     * @param ID $Id
     *
     * @access public
     */
    public function __construct($sobjectType = null, $Id = null)
    {
        parent::__construct(array());

        $this->Id = $Id;

        $this->sobjectType = $sobjectType;
    }

    /**
     * Returns the NULL fields to (re-)set to NULL
     *
     * @return array $fieldsToNull
     */
    public function getFieldsToNull()
    {
        $retVal = array();

        foreach($this AS $key => $value)
        {
            if(null === $value || '' === $value)
            {
                $retVal[] = $key;
            }
        }

        return $retVal;
    }

    /**
     * getId()
     *
     * @return \ID
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * getSobjectType()
     *
     * If not set, the class tries to determine
     * one out of the callee classname.
     *
     * @return string
     */
    public function getSobjectType()
    {
        if(null === $this->sobjectType)
        {
            $pathname = get_called_class();

            if(false === ($strpos = strrpos($pathname, '\\')))
            {
                $this->sobjectType = $pathname;
            }
            else
            {
                $this->sobjectType = substr($pathname, $strpos  + 1);
            }
        }
        return $this->sobjectType;
    }
}
