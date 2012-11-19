<?php
namespace Codemitte\ForceToolkit\Form\Type;

use
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\Options
;

class CurrencyType extends TextType
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
                'precision' => function(Options $options, $previous)
                {
                    if(null !== ($precision = $options['field']->getPrecision()))
                    {
                        return $precision;
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
        return 'money';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sfdc_currency';
    }
}
