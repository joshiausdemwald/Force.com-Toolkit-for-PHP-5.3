<?php
/**
 * Copyright (C) 2012 code mitte GmbH - Zeughausstr. 28-38 - 50667 Cologne/Germany
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so, subject
 * to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Codemitte\ForceToolkit\Form\Metadata;

use Codemitte\ForceToolkit\Soap\Mapping\Field;

/**
 * FormField
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Form
 *
 * @abstract
 */
class FormField extends FormWidget
{
    /**
     * @var int
     */
    private $displayLines;

    /**
     * @var FormField
     */
    private $field;

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor.
     *
     * @param $tabOrder
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Field $field
     * @param string|null $value
     * @param int|null $displayLines
     * @param $internalId
     * @param \Codemitte\ForceToolkit\Form\Metadata\ComponentInterface $parent
     * @param \Codemitte\ForceToolkit\Form\Metadata\ComponentInterface $root
     */
    public function __construct($tabOrder, Field $field, $value = null, $displayLines = null, $internalId, ComponentInterface $parent, ComponentInterface $root)
    {
        parent::__construct($tabOrder, $value, $internalId, $parent, $root);

        $this->field = $field;

        $this->displayLines = $displayLines;

        $this->name = $this->field->getName();
    }

    /**
     * The number of vertical lines displayed for a field.
     * Applies to textarea and multi-select picklist fields.
     *
     * @return int
     */
    public function getDisplayLines()
    {
        return $this->displayLines;
    }

    /**
     * getField()
     *
     * @return Field $field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns true if the field is editable as
     * specified on the page layout.
     *
     *
     * @param bool $is_create
     *
     * @return bool $editable
     */
    public function getEditable($is_create = true)
    {
        return
            (
                $this->getParent()->getEditable() ||
                $this->getParent()->getRequired()
            ) &&
            ! $this->getField()->getCalculated() &&
            ! $this->getField()->getAutoNumber() &&
            (
                $is_create && $this->getField()->getCreateable() ||
                ! $is_create && $this->getField()->getUpdateable()
            )
        ;
    }
}