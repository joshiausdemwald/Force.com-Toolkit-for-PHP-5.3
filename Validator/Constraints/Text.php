<?php
namespace Codemitte\ForceToolkit\Validator\Constraints;

use
    Symfony\Component\Validator\Constraint
;

/**
 * @Annotation
 */
class Text extends Constraint
{
    public $maxLengthMessage = 'The value you typed exceeds the maximum length.';

    public $sObjectType;

    public $fieldname;

    public $recordTypeId;

    /**
     * Returns the name of the required options
     *
     * Override this method if you want to define required options.
     *
     * @return array
     * @see __construct()
     *
     * @api
     */
    public function getRequiredOptions()
    {
        return array
        (
            'sObjectType',
            'fieldname'
        );
    }

    /**
     * Returns the service name instead of classname
     *
     * @override
     * @return string
     */
    public function validatedBy()
    {
        return 'forcetk_text_validator';
    }
}
