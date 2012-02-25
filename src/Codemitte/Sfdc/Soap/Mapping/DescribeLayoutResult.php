<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

class DescribeLayoutResult implements ClassInterface
{
   /**
     * @var array $layouts
     */
    private $layouts;

    /**
     * @var array $recordTypeMappings
     */
    private $recordTypeMappings;

    /**
     *
     * @var boolean $recordTypeSelectorRequired
     */
    private $recordTypeSelectorRequired;

    /**
     * Constructor.
     *
     * @param array $layouts
     * @param array $recordTypeMappings
     * @param boolean $recordTypeSelectorRequired
     *
     * @access public
     */
    public function __construct(array $layouts, array $recordTypeMappings, $recordTypeSelectorRequired)
    {
        $this->layouts                    = $layouts;
        $this->recordTypeMappings         = $recordTypeMappings;
        $this->recordTypeSelectorRequired = $recordTypeSelectorRequired;
    }

    /**
     * @return array
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * @return array
     */
    public function getRecordTypeMappings()
    {
        return $this->recordTypeMappings;
    }

    /**
     * @return boolean
     */
    public function getRecordTypeSelectorRequired()
    {
        return $this->recordTypeSelectorRequired;
    }
}
