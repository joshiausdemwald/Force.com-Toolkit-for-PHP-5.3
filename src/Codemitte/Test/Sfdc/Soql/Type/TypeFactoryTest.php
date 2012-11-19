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

namespace Codemitte\Test\Sfdc\Soql\Type;

use \PHPUnit_Framework_TestCase;

use Codemitte\ForceToolkit\Soql\Type\TypeFactory;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Test
 * @subpackage Sfdc
 */
class TypeFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testString()
    {
        $typeFactory = new TypeFactory();

        $string = $typeFactory->create('teststring');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\String', $string);

        $string = $typeFactory->create('1234');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\String', $string);

        $string = $typeFactory->create('1234.123');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\String', $string);
    }

    public function testNumber()
    {
        $typeFactory = new TypeFactory();

        $number = $typeFactory->create(1234);

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $number);

        $number = $typeFactory->create('1234');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $number);

        $number = $typeFactory->create(1234.123);

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $number);

        $number = $typeFactory->create('1234.123');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $number);
    }

    public function testDate()
    {
        $typeFactory = new TypeFactory();

        $date = $typeFactory->create('2011-02-13');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Date', $date);

        $date = $typeFactory->create('20-02-13');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\Date', $date);

        $date = $typeFactory->create('-02-13');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\Date', $date);

        $date = $typeFactory->create('-2012-02-13');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\Date', $date);

        $date = $typeFactory->create('1989-16-52');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\Date', $date);
    }

    public function testDateTime()
    {
        $typeFactory = new TypeFactory();

        $date = $typeFactory->create('2011-02-13T12:32:12Z');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\DateTime', $date);

        $this->assertEquals('2011-02-13T12:32:12+00:00', $date->toSOQL());

        $date = $typeFactory->create('2011-02-13Z12:32:12-07:32');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\DateTime', $date);

        $date = $typeFactory->create('2011-02-13 12:32');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\DateTime', $date);

        $date = $typeFactory->create('-2011-02-13Z12:32:12-07:32');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\DateTime', $date);

        $date = $typeFactory->create('2011-02-78Z12:32:12-07:32');

        $this->assertNotInstanceOf('Codemitte\ForceToolkit\Soql\Type\DateTime', $date);
    }

    public function testBoolean()
    {
        $typeFactory = new TypeFactory();

        $bool = $typeFactory->create(true);

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Boolean', $bool);

        $this->assertEquals(true, $bool->getPHPValue());

        $bool = $typeFactory->create(false);

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Boolean', $bool);

        $this->assertEquals(false, $bool->getPHPValue());
    }

    public function testArrayType()
    {
        $typeFactory = new TypeFactory();

        $array = $typeFactory->create(array(
            'This is a string',
            1234,
            1234.5,
            '2011-02-03',
            '2011-02-03T12:32+06:30',
            true,
            false,
            array(
                'This is a string',
                1234,
                1234.5,
                '2011-02-03',
                '2011-02-03T12:32+06:30',
                true,
                false
            )
        ));

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\ArrayType', $array);

        $values = $array->getPHPValue();

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\String', $values[0]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $values[1]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $values[2]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Date', $values[3]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\DateTime', $values[4]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Boolean', $values[5]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Boolean', $values[6]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\ArrayType', $values[7]);

        $values = $values[7]->getPHPValue();

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\String', $values[0]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $values[1]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Number', $values[2]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Date', $values[3]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\DateTime', $values[4]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Boolean', $values[5]);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Type\Boolean', $values[6]);
    }

    public function testExpression()
    {
        $e = new \Codemitte\ForceToolkit\Soql\Type\Expression("ping");

        $this->assertEquals('ping', $e->toSOQL());
    }
}
