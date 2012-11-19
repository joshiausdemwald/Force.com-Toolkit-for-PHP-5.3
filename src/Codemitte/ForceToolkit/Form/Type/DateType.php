<?php
namespace Codemitte\ForceToolkit\Form\Type;

use
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Locale\Locale
;

class DateType extends TextType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $format = $options['format'];
        $timezone = $options['data_timezone'];
        $locale = Locale::getDefault();

        $formatter = new \IntlDateFormatter($locale, $format, \IntlDateFormatter::NONE, $timezone, \IntlDateFormatter::GREGORIAN);

        $view->vars['attr']['data-locale']      = Locale::getDefault();
        $view->vars['attr']['data-format']      = $formatter->getPattern();
        $view->vars['attr']['data-timezone']    = $timezone;
    }


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
                // ATOM/W3C
                'input' => 'string',
                'format' => \IntlDateFormatter::MEDIUM,
                'widget' => 'single_text'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sfdc_date';
    }
}
