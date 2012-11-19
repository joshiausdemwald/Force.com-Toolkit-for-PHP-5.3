<?php
namespace Codemitte\Form\Type;

use Symfony\Component\Form\AbstractType;

class PhoneType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phone';
    }
}
