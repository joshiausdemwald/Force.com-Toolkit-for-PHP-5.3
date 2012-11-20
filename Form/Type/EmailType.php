<?php
namespace Codemitte\ForceToolkit\Form\Type;

class EmailType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'forcetk_email';
    }
}
