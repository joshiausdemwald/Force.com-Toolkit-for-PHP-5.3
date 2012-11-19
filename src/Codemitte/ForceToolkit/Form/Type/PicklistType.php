<?php
namespace Codemitte\ForceToolkit\Form\Type;

use
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\Exception\FormException,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\Options,

    Codemitte\ForceToolkit\Form\Model\PicklistChoiceList,
    Codemitte\ForceToolkit\Form\Model\PicklistChoiceListInterface,
    Codemitte\ForceToolkit\Soap\Mapping\Type\fieldType AS SfdcType,

    Codemitte\Form\Form\RecursiveFormIterator
;

class PicklistType extends AbstractSfdcType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setAttribute('valid_for', $options['valid_for']);
        $builder->setAttribute('controlling_fieldname', $options['controlling_fieldname']);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        
        $view->vars['data_dependent_picklist'] = null;
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        if($form->hasAttribute('controlling_fieldname') && ($controlling_fieldname = $form->getAttribute('controlling_fieldname')))
        {
            foreach(new \RecursiveIteratorIterator(new RecursiveFormIterator($form->getRoot()), \RecursiveIteratorIterator::SELF_FIRST) AS $childForm)
            {
                /**  @var $childForm FormInterface */
                if($childForm->hasAttribute('fieldname') && $childForm->getAttribute('fieldname') === $controlling_fieldname)
                {
                    // @TODO: CALCULATE TARGET ID -- THIS IS NOT RELIABLE IN EACH SITUATION, BUT AT FIRST IT'LL FIT
                    $name = $childForm->getName();

                    $targetId = null;

                    if (null !== $view->parent)
                    {
                        $parentId = isset($view->parent->vars['id']) ? $view->parent->vars['id'] : null;

                        $targetId = sprintf('%s_%s', $parentId, $name);
                    }
                    else
                    {
                        $targetId = $name;
                    }

                    // IDENTIFY ID ATTRIBUTE
                    $data = array(
                        'controlling_field' => $targetId,
                        'valid_for'         => $form->getAttribute('valid_for')
                    );

                    $view->vars['data_dependent_picklist'] = json_encode($data);
                }
            }
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     * @throws \Symfony\Component\Form\Exception\FormException
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $self = $this;

        $resolver
        ->setOptional(array(
            'valid_for',
            'controlling_fieldname'
        ))
        ->setDefaults(array(
            'valid_for' => function(Options $options)
            {
                $field = $options['field'];

                if($field->isDependentPicklist())
                {
                    $retVal = array();

                    foreach($field->getPicklistEntries() AS $picklistEntry)
                    {
                        $retVal[$picklistEntry->getValue()] = utf8_decode($picklistEntry->getValidFor());
                    }
                    return $retVal;
                }
                return null;
            },
            'controlling_fieldname' => function(Options $options)
            {
                $field = $options['field'];

                if($field->isDependentPicklist())
                {
                    return $field->getControllingFieldname();
                }
                return null;
            },
            'empty_value' => function(Options $options)
            {
                if( ! $options['multiple'])
                {
                    return 'Please select';
                }
            },
            'multiple' => function(Options $options)
            {
                return (string)$options['field']->getType() === SfdcType::multipicklist ? true :false;
            },
            'choice_list' => function(Options $options) use ($self)
            {
                if( ! isset ($options['field']))
                {
                    throw new FormException('Either a "field" option, a "sobject_type" + "fieldname" option or a "choice_list" option must be provided.');
                }

                $field = $options['field'];

                return new PicklistChoiceList
                (
                    $field,
                    (isset($options['filter']) ? $options['filter'] : null)
                );
            }
        ))
        ->setNormalizers(array(
            'field' => function (Options $options, $field)
            {
                if(null === $field)
                {
                    if( ! isset($options['choice_list']))
                    {
                        throw new FormException('Either a "field" option, a "sobject_type" + "fieldname" option or a "choice_list" option must be provided.');
                    }

                    if( ! $options['choice_list'] instanceof PicklistChoiceListInterface)
                    {
                        throw new FormException('Option "choice_list" must contain an instanceof PicklistChoiceListInterface.');
                    }
                    return $options['choice_list']->getField();
                }
                return $field;
            }
        ));
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
        return 'sfdc_picklist';
    }
}
