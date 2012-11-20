<?php
namespace Codemitte\ForceToolkit\Form\Type;

class PhoneType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'phone';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'forcetk_phone';
    }
}
