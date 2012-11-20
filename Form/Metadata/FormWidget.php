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

/**
 * FormWidget
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Form
 *
 * @abstract
 */
class FormWidget extends Component
{
    /**
     * @var int
     */
    private $tabOrder;

    /**
     * @var string
     */
    private $value;

    /**
     * Constructor.
     *
     * @param $tabOrder
     * @param $value
     * @param $internalId
     * @param \Codemitte\ForceToolkit\Form\Metadata\ComponentInterface $parent
     * @param \Codemitte\ForceToolkit\Form\Metadata\ComponentInterface $root
     */
    public function __construct($tabOrder, $value, $internalId, ComponentInterface $parent, ComponentInterface $root)
    {
        parent::__construct($internalId, $parent, $root);

        $this->value         = $value;

        $this->tabOrder      = $tabOrder;
    }

    /**
     * Indicates the tab order for the item in the row.
     *
     * @return int
     */
    public function getTabOrder()
    {
        return $this->tabOrder;
    }

    /**
     * Value of this LayoutComponent. The name of the field
     * if the LayoutComponentType value is Field.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
