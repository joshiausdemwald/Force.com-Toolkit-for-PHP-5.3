<?php
namespace Codemitte\Sfdc\Soql\AST;

class GroupPart extends AbstractSoqlPart
{
    /**
     * @var array
     */
    private $groupFields;

    /**
     * @var bool
     */
    private $isCube;

    /**
     * @var bool
     */
    private $isRollup;

    public function __construct()
    {
        $this->groupFields = array();
    }

    /**
     * @param \Codemitte\Sfdc\Soql\AST\GroupableInterface $field
     * @return void
     * @internal param $ \Codemitte\Sfdc\Soql\AST\Selectable
     */
    public function addGroupField(GroupableInterface $field)
    {
        $this->groupFields[] = $field;
    }

    /**
     * @param array<GroupableInterface> $fields
     */
    public function addGroupFields(array $fields)
    {
        foreach($fields AS $field)
        {
            $this->addGroupField($field);
        }
    }

    /**
     * @param boolean $isCube
     */
    public function setIsCube($isCube = true)
    {
        $this->isCube = $isCube;
    }

    /**
     * @param boolean $isRollup
     */
    public function setIsRollup($isRollup = true)
    {
        $this->isRollup = $isRollup;
    }

    /**
     * @return boolean
     */
    public function getIsCube()
    {
        return $this->isCube;
    }

    /**
     * @return boolean
     */
    public function getIsRollup()
    {
        return $this->isRollup;
    }

    /**
     * @return array
     */
    public function getGroupFields()
    {
        return $this->groupFields;
    }
}
