<?php
namespace Codemitte\ForceToolkit\Form\Type;

use
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Locale\Locale,
    Symfony\Component\Form\Extension\Core\Type\DateTimeType AS SfDateTimeType
;

class DateTimeType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $date_format = $options['date_format'];

        $time_format = SfDateTimeType::DEFAULT_TIME_FORMAT;

        $timezone = $options['data_timezone'];

        $locale = Locale::getDefault();

        $formatter_date = new \IntlDateFormatter($locale, $date_format , \IntlDateFormatter::NONE, $timezone, \IntlDateFormatter::GREGORIAN);
        $formatter_time = new \IntlDateFormatter($locale, \IntlDateFormatter::NONE, $time_format, $timezone, \IntlDateFormatter::GREGORIAN);

        $view->vars['attr']['data-locale'] = Locale::getDefault();
        $view->vars['attr']['data-date_format'] = $formatter_date->getPattern();
        $view->vars['attr']['data-time_format'] = $formatter_time->getPattern();
        $view->vars['attr']['data-timezone'] = $timezone;
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
                'date_format' => \IntlDateFormatter::MEDIUM,
                'with_seconds' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'datetime';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'forcetk_datetime';
    }
}
