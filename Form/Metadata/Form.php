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
 * Form
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Form
 *
 * @abstract
 */
class Form extends Component implements FormInterface
{
    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var string
     */
    private $name;

    /**
     * @var array|null
     */
    private $requestData;

    /**
     * @var boolean
     */
    private $isNew;

    /**
     * Constructor.
     *
     * @param $internalId
     */
    public function __construct($internalId)
    {
        parent::__construct($internalId, null, null);
    }

    public function addField(FormField $field)
    {
        $this->fields[$field->getValue()] = $field;
    }

    public function getField($name)
    {
        return $this->fields[$name];
    }

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * setName()
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param $requestData
     */
    public function bind($requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     *
     * @return boolean
     */
    public function isBound()
    {
        return null !== $this->requestData;
    }

    /**
     * @return boolean
     */
    public function isNew($isNew = null)
    {
        if($isNew !== null)
        {
            $this->isNew = $isNew;
        }
        return $this->isNew;
    }
}
