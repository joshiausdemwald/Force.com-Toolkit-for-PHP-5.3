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

namespace Codemitte\ForceToolkit\Form\Builder;

use \OutOfBoundsException;

use Codemitte\ForceToolkit\Soap\Mapping\DescribeLayout;
use Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult;
use Codemitte\ForceToolkit\Soap\Mapping\Type\layoutComponentType;
use Codemitte\ForceToolkit\Soap\Mapping\Type\fieldType;

use Codemitte\ForceToolkit\Form\Metadata;


/**
 * FormMetadataBuilder
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte
 * GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Form
 */
class FormMetadataBuilder implements MetadataBuilderInterface
{
    /**
     * @var DescribeLayout
     */
    private $describeLayout;

    /**
     * @var Sobject
     */
    private $sObjectDescribe;

    /**
     * Constructor
     *
     * @param DescribeLayout $describeLayout
     *
     * @param DescribeSObjectResult $describeSobjectResult
     */
    public function __construct(DescribeLayout $describeLayout, DescribeSObjectResult $describeSobjectResult)
    {
        $this->describeLayout = $describeLayout;

        $this->sObjectDescribe = new Metadata\Sobject($describeSobjectResult);
    }

    /**
     * @throws MetadataBuilderException
     * @return \Codemitte\ForceToolkit\Form\Metadata\Form
     */
    public function build()
    {
        static $cnt = 0;

        $formId = 'sfdc-f-' . $cnt;

        $cnt ++;

        $form = new Metadata\Form($formId);

        foreach($this->describeLayout->getEditLayoutSections() AS $i => $section)
        {
            $fieldsetId = $formId . '-' . $i;

            /* @var $section DescribeLayoutSection */
            $form->addChild(($fieldset = new Metadata\FormFieldset(($section->getHeading() && $section->getUseHeading() ? $section->getHeading() : null), $fieldsetId, $form, $form)));

            $layoutRows = $section->getLayoutRows();

            for($j = 0; $j < $section->getRows(); $j++)
            {
                $rowId = $fieldsetId . '-' . $j;

                $fieldset->addChild(($row = new Metadata\FormRow($rowId, $fieldset, $form)));

                $layoutItems = $layoutRows[$j]->getLayoutItems();

                // COL
                for($k = 0; $k < $section->getColumns(); $k ++)
                {
                    $colId = $rowId . '-' . $k;

                    /* @var $layoutItem DescribeLayoutItem */
                    $layoutItem = $layoutItems[$k];

                    $col = null;

                    if($layoutItem->getPlaceholder())
                    {
                        $col = new Metadata\FormPlaceholder($layoutItem->getLabel(), $colId, $row, $form);
                    }
                    else
                    {
                        $col = new Metadata\FormCol(
                            $layoutItem->getLabel(),
                            $layoutItem->getEditable(),
                            $layoutItem->getRequired(),
                            $colId,
                            $row,
                            $form
                        );
                    }
                    $row->addChild($col);

                    if(null !== $layoutItem->getLayoutComponents())
                    {
                        foreach($layoutItem->getLayoutComponents() AS $l => $layoutComponent)
                        {
                            $fieldId = $colId . '-' . $l;

                            $child = null;

                            /* @var $layoutComponent DescribeLayoutComponent */
                            switch((string)$layoutComponent->getType())
                            {
                                // Field—Field name. A mapping to the name field on the describeSObjectResult.
                                case layoutComponentType::Field:

                                    try
                                    {
                                        /* @var $field Field */
                                        $field = $this->sObjectDescribe->getField($layoutComponent->getValue());

                                        $class = null;

                                        switch((string)$field->getType())
                                        {
                                            case fieldType::url:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldUrl';
                                                break;
                                            case fieldType::email:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldEmail';
                                                break;
                                            case fieldType::base64:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldBase64';
                                                break;
                                            case fieldType::boolean:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldBoolean';
                                                break;
                                            case fieldType::combobox:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldCombobox';
                                                break;
                                            case fieldType::currency:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldCurrency';
                                                break;
                                            case fieldType::datacategorygroupreference:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldDataCategoryGroupPreference';
                                                break;
                                            case fieldType::date:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldDate';
                                                break;
                                            case fieldType::datetime:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldDateTime';
                                                break;
                                            case fieldType::time:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldTime';
                                                break;
                                            case fieldType::double:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldDouble';
                                                break;
                                            case fieldType::encryptedstring:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldEncryptedString';
                                                break;
                                            case fieldType::int:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldInt';
                                                break;
                                            case fieldType::id:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldId';
                                                break;
                                            case fieldType::picklist:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldPicklist';
                                                break;
                                            case fieldType::multipicklist:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldMultiPicklist';
                                                break;
                                            case fieldType::percent:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldPercent';
                                                break;
                                            case fieldType::phone:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldPhone';
                                                break;
                                            case fieldType::textarea:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldTextarea';
                                                break;
                                            case fieldType::reference:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldReference';
                                                break;
                                            default:
                                            case fieldType::string:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\FormFieldString';
                                                break;
                                            case fieldType::anyType:
                                                $class = 'Codemitte\ForceToolkit\Form\Metadata\AnyType';
                                                break;
                                        }
                                        $child = new $class(
                                            $layoutComponent->getTabOrder(),
                                            $field,
                                            $layoutComponent->getValue(),
                                            $layoutComponent->getDisplayLines(),
                                            $fieldId,
                                            $col,
                                            $form
                                        );
                                    }

                                    // FIELD COULD NOT BE FOUND ...
                                    catch(OutOfBoundsException $e)
                                    {
                                        throw new MetadataBuilderException(sprintf('Tried to address a non-existing field "%s", referenced in layout "%s"', $layoutComponent->getValue(), $this->describeLayout->getId()), null, $e);
                                    }
                                    break;

                                // EmptySpace—A blank space on the page layout.
                                case layoutComponentType::EmptySpace:
                                    $child = new Metadata\Spacer(
                                        $layoutComponent->getTabOrder(),
                                        $layoutComponent->getValue(),
                                        $fieldId,
                                        $col,
                                        $form
                                    );
                                    break;

                                // Separator—Separator character, such as a semicolon (:) or slash (/).
                                case layoutComponentType::Separator:
                                    $child = new Metadata\Separator(
                                        $layoutComponent->getTabOrder(),
                                        $layoutComponent->getValue(),
                                        $fieldId,
                                        $col,
                                        $form
                                    );
                                    break;

                                // IGNORE S-CONTROL (SControl—Reserved for future use.)
                                default:
                                    throw new MetadataBuilderException(sprintf('Unsupported form component "%s"', $layoutComponent->getType()));
                            }
                            $col->addChild($child);
                        }
                    }
                }
            }
        }
        return $form;
    }
}
