<?php
namespace Codemitte\ForceToolkit\Form\Type;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\Exception\FormException,

    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\Options,

    Symfony\Component\Form\FormBuilderInterface,

    Codemitte\ForceToolkit\Metadata\DescribeFormFactoryInterface
;

abstract class AbstractSfdcType extends AbstractType implements SfdcTypeInterface
{
    /**
     * @var callable
     */
    protected $fieldOption;

    /**
     * @var \Codemitte\ForceToolkit\Metadata\DescribeFormFactoryInterface
     */
    protected $describeFormFactory;

    /**
     * @param \Codemitte\ForceToolkit\Metadata\DescribeFormFactoryInterface $describeFormFactory
     * @throws \Symfony\Component\Form\Exception\FormException
     */
    public function __construct(DescribeFormFactoryInterface $describeFormFactory)
    {
        $this->describeFormFactory = $describeFormFactory;

        $self = $this;

        $this->fieldOption = function(Options $options) use ($self)
        {
            if( ! isset($options['sobject_type']))
            {
                throw new FormException('Either "sobject_type" or "field" option must be set.');
            }

            if( ! isset($options['fieldname']))
            {
                throw new FormException('Either "fieldname" or "field" option must be set.');
            }

            $describeFormResult = $self->getDescribeFormFactory()->getDescribe(
                $options['sobject_type'],
                $options['recordtype_id']
            );

            $retVal = $describeFormResult->getField($options['fieldname']);

            if(null === $retVal)
            {
                throw new FormException(sprintf('A field with fieldname "%s" doesn`t exist on sobject "%s".', $options['fieldname'], $options['sobject_type']));
            }

            return $retVal;
        };
    }

    /**
     * @return \Codemitte\ForceToolkit\Metadata\DescribeFormFactoryInterface
     */
    public function getDescribeFormFactory()
    {
        return $this->describeFormFactory;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setAttribute('fieldname', $options['fieldname']);
        $builder->setAttribute('field_type', $options['field_type']);
    }


    /**
     * Inherited options:
     *  disabled
     *  required
     *  label
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'field'
            ))
            ->setOptional(array(
                'sobject_type',
                'fieldname',
                'recordtype_id'
            ))
            ->setDefaults(array(
                'recordtype_id' => null,
                'field' => $this->fieldOption,
                'sobject_type' => function(Options $options)
                {
                    return $options['field']->getSobjectType();
                },
                'fieldname' => function(Options $options)
                {
                    return $options['field']->getName();
                },
                'field_type' => function(Options $options)
                {
                    return $options['field']->getType();
                },
                'required' => function(Options $options, $previous)
                {
                    if(null !== ($req= $options['field']->getRequired()))
                    {
                        return $req;
                    }
                    return $previous;
                },
                'label' => function(Options $options, $previous)
                {
                    if(null !== ($label = $options['field']->getLabel()))
                    {
                        return $label;
                    }
                    return $previous;
                },
                'read_only' => function(Options $options, $previous)
                {
                    if(null !== ($readonly = $options['field']->getReadonly()))
                    {
                        return $readonly;
                    }
                    return $previous;
                }
            ));
    }
}
