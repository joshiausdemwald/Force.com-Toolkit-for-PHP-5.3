<?php
namespace Codemitte\ForceToolkit\Form\Model;

use
    Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

interface PicklistChoiceListInterface extends ChoiceListInterface
{
    /**
     * @return \Codemitte\ForceToolkit\Metadata\FieldInterface
     */
    public function getField();

    /**
     * @abstract
     * @return array
     */
    public function getFilter();
}