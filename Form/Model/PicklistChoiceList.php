<?php
namespace Codemitte\ForceToolkit\Form\Model;

use
    Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList,
    Codemitte\ForceToolkit\Metadata\FieldInterface;

class PicklistChoiceList extends SimpleChoiceList implements PicklistChoiceListInterface
{
    /**
     * @var \Codemitte\ForceToolkit\Metadata\Field
     */
    private $field;

    /**
     * @var array
     */
    private $filter;

    /**
     * Constructor.
     *
     * @param \Codemitte\ForceToolkit\Metadata\FieldInterface $field
     * @param array|null $filter
     * @throws \RuntimeException
     * @return \Codemitte\ForceToolkit\Form\Model\PicklistChoiceList
     */
    public function __construct(FieldInterface $field, array $filter = null)
    {
        $this->field = $field;

        $this->filter = $filter;

        $choices = array();

        if(null === $this->field->getPicklistEntries())
        {
            throw new \RuntimeException('No picklist entries given. Correct field mapping?');
        }
        foreach($this->field->getPicklistEntries() AS $entry)
        {
            /** @var $entry \Codemitte\ForceToolkit\Soap\Mapping\PicklistEntry */
            if(is_array($this->filter) && ! in_array($entry->getValue(), $this->filter))
            {
                continue;
            }

            $choices[$entry->getValue()] = $entry->getLabel();
        }
        parent::__construct($choices);
    }

    /**
     * @return \Codemitte\ForceToolkit\Metadata\Field|\Codemitte\ForceToolkit\Metadata\FieldInterface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }
}