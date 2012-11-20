<?php
namespace Codemitte\ForceToolkit\Form\Type;

use
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\Options
;

class TextType extends AbstractSfdcType
{
    /**
     * Inherited options:
     *  max_length
     *  required
     *  label
     *  trim
     *  read_only
     *  error_bubbling
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'max_length' => function(Options $options, $previous)
                {
                    if(null !== ($len = $options['field']->getMaxLength()))
                    {
                        return $len;
                    }
                    return $previous;
                }
        ));
    }

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
        return 'forcetk_text';
    }
}
