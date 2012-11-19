<?php
namespace Codemitte\ForceToolkit\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TextValidator extends AbstractValidator
{
    /**
     * @param $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     * @return bool
     * @throws \Exception
     */
    function validate($value, Constraint $constraint)
    {
        if( ! $value)
        {
            return;   // REQUIRED CONSTRAINT (!)
        }

        try
        {
            $describeFormResult = $this->describeFormFactory->getDescribe(
                $constraint->sObjectType,
                $constraint->recordTypeId
            );

            /** @var $field \Codemitte\ForceToolkit\Metadata\Field */
            $field = $describeFormResult->getField($constraint->fieldname);

            $value = (string)$value;

            if($field->getMaxLength() && strlen($value) > $field->getMaxLength())
            {
                $this->context->addViolation($constraint->maxLengthMessage, array('{{ value }}' => $value));
            }
        }
        catch(\Exception $e)
        {
            throw $e;
        }
        return;
    }
}
