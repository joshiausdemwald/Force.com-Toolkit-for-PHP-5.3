<?php
namespace Codemitte\ForceToolkit\Metadata;

interface DescribeFormResultInterface
{
    /**
     * Returns all fields as an (ordered) list.
     *
     * @abstract
     * @return array<FieldInterface>
     */
    public function getFields();

    /**
     * Returns a specific field definition by it's name.
     *
     * @abstract
     * @param $fieldname
     * @return FieldInterface $field
     */
    public function getField($fieldname);

    /**
     * Adds field to the list of fields.
     *
     * @abstract
     * @param \Codemitte\ForceToolkit\Metadata\FieldInterface $field
     * @return void
     */
    public function addField(FieldInterface $field);
}
