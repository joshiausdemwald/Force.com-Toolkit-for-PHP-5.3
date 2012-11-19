<?php
namespace Codemitte\ForceToolkit\Form\Type;

class TextareaType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sfdc_textarea';
    }
}
