<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class DescribeLayout implements ClassInterface
{
    /**
     * @var DescribeLayoutButtonSection $buttonLayoutSection
     */
    private $buttonLayoutSection;

    /**
     * @var DescribeLayoutSection $detailLayoutSections
     */
    private $detailLayoutSections;

    /**
     * @var DescribeLayoutSection $editLayoutSections
     */
    private $editLayoutSections;

    /**
     * @var ID $id
     */
    private $id;

    /**
     * @var RelatedList $relatedLists
     * @access public
     */
    private $relatedLists;

    /**
     *
     * @param DescribeLayoutButtonSection $buttonLayoutSection
     * @param DescribeLayoutSection $detailLayoutSections
     * @param DescribeLayoutSection $editLayoutSections
     * @param ID $id
     * @param RelatedList $relatedLists
     *
     * @access public
     */
    public function __construct($buttonLayoutSection, $detailLayoutSections, $editLayoutSections, $id, $relatedLists)
    {
        $this->buttonLayoutSection  = $buttonLayoutSection;
        $this->detailLayoutSections = $detailLayoutSections;
        $this->editLayoutSections   = $editLayoutSections;
        $this->id                   = $id;
        $this->relatedLists         = $relatedLists;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\DescribeLayoutButtonSection
     */
    public function getButtonLayoutSection()
    {
        return $this->buttonLayoutSection;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\DescribeLayoutSection
     */
    public function getDetailLayoutSections()
    {
        return $this->detailLayoutSections;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\DescribeLayoutSection
     */
    public function getEditLayoutSections()
    {
        return $this->editLayoutSections;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\RelatedList
     */
    public function getRelatedLists()
    {
        return $this->relatedLists;
    }
}
