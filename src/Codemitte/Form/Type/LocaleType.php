<?php
namespace Codemitte\Form\Type;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\Locale\Locale,
    Codemitte\Bundle\ForceBundle\DependencyInjection\PortalConfigInterface
;

class LocaleType extends AbstractType
{
    /**
     * @var \Codemitte\Bundle\ForceBundle\DependencyInjection\PortalConfigInterface
     */
    private $portalConfig;

    private static $translation = array(
        'en' => 'English',
        'en_US' => 'English (USA)',
        'en_GB' => 'English (Great Britain)',
        'it' => 'Italiano',
        'it_IT' => 'Italiano (Italia)',
        'it_CH' => 'Italiano (Svizzera)',
        'de' => 'Deutsch',
        'de_DE' => 'Deutsch (Deutschland)',
        'de_AT' => 'Deutsch (Österreich)',
        'de_CH' => 'Deutsch (Schweiz)',
    );

    /**
     * @param \Codemitte\Bundle\ForceBundle\DependencyInjection\PortalConfigInterface $portalConfig
     */
    public function __construct(PortalConfigInterface $portalConfig)
    {
        $this->portalConfig = $portalConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $locales = Locale::getLocales();

        $availableLocales = $this->portalConfig->getAvailableLocales();

        if(null !== $availableLocales)
        {
            $locales = array_intersect($locales, $availableLocales);
        }

        $choices = array();

        foreach($locales AS $locale)
        {
            if(isset(self::$translation[$locale]))
            {
                $choices[$locale] = self::$translation[$locale];
            }
            else
            {
                $choices[$locale] = $locale;
            }
        }

        $defaults = array(
            'choices' => $choices
        );

        /*if(count($locales) < 2)
        {
            $defaults['readonly'] = true;
            $defaults['disabled'] = true;
        }*/

        $resolver->setDefaults($defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'friendly_locale';
    }
}
