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
 * Component
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Form
 *
 * @abstract
 */
abstract class Component implements ComponentInterface
{
    /**
     * @var ComponentInterface|null
     */
    private $parent;

    /**
     * @var array
     */
    private $children = array();

    /**
     * @var string
     */
    private $internalId;

    /**
     * @var ComponentInterface
     */
    private $root;

    /**
     * Constructor.
     *
     * @param $internalId
     * @param ComponentInterface $parent
     * @param \Codemitte\ForceToolkit\Form\Metadata\ComponentInterface|null $root
     */
    public function __construct($internalId, ComponentInterface $parent = null, ComponentInterface $root = null)
    {
        $this->internalId = $internalId;

        $this->parent = $parent;

        $this->root = $root;
    }

    /**
     * Returns the internal id of this component
     *
     * @return string
     */
    public function getInternalId()
    {
        return $this->internalId;
    }

    /**
     * addChild()
     *
     * @param \Codemitte\ForceToolkit\Form\View\Component|\Codemitte\ForceToolkit\Form\View\ComponentInterface $component
     */
    public function addChild(ComponentInterface $component)
    {
        $this->children[] = $component;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Returns if an iterator can be created fot the current entry.
     *
     * @link http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     */
    public function hasChildren()
    {
        return $this->current() instanceof ComponentInterface && count($this->current()->getChildren()) > 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->children);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->children);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, integer
     * 0 on failure.
     */
    public function key()
    {
        return key($this->children);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->children);
    }

    /**
     * getParent()
     *
     * @return ComponentInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * getRoot()
     *
     * @return ComponentInterface
     */
    public function getRoot()
    {
        return $this->root;
    }

}
