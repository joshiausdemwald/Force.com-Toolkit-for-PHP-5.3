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

namespace Codemitte\Test\Sfdc\Soql\Parser;

use \PHPUnit_Framework_TestCase;

use Codemitte\ForceToolkit\Soql\Parser\QueryParser;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Test
 * @subpackage Sfdc
 */
class QueryParserTest extends PHPUnit_Framework_TestCase
{
    public function testParser1()
    {
        $parser = new QueryParser();

        $result = $parser->parse('
          SELECT
            ID
          FROM
            eps_Loyalty__c
          WHERE
            name = :eins AND ping IN (:eins, :zwei)
            AND test = \':testerer_in_\\\'literal\'
            pong IN (:eins, :zwei, :drei)
            AND
              pingpong = :zwei
            AND
              test=\'a literal to die for\' AND var=:drei AND lit2=\'literal 2 value\'
            AND dings IN :vier
            AND bool = :bool
            AND datefield = :String.valueOf(\'pingpong\')
            AND date = Date.today()
            AND testtokenizer = \'XXX\'
            AND testtokenizer2 = \'YYY\'
            AND testtest = :drei
            AND date < LAST_FISCAL_QUARTER
            AND date > NEXT_N_FISCAL_YEARS:3
            AND date < 2011-12-02
          ORDER BY hanswurst
          GROUP BY
            Id',

                array(
                     'eins' => 0.127,
                     'zwei' => 'pong\'hansiwurst',
                     'drei' => true,
                     'vier' => array('eins', 'zwei', 'drei', 'vier'),
                     'bool' => true
                ));


        $this->assertEquals("
          SELECT
            ID
          FROM
            eps_Loyalty__c
          WHERE
            name = 0.127 AND ping IN (0.127, 'pong\'hansiwurst')
            AND test = ':testerer_in_\'literal'
            pong IN (0.127, 'pong\'hansiwurst', true)
            AND
              pingpong = 'pong\'hansiwurst'
            AND
              test='a literal to die for' AND var=true AND lit2='literal 2 value'
            AND dings IN ('eins', 'zwei', 'drei', 'vier')
            AND bool = true
            AND datefield = String.valueOf('pingpong')
            AND date = Date.today()
            AND testtokenizer = 'XXX'
            AND testtokenizer2 = 'YYY'
            AND testtest = true
            AND date < LAST_FISCAL_QUARTER
            AND date > NEXT_N_FISCAL_YEARS:3
            AND date < 2011-12-02
          ORDER BY hanswurst
          GROUP BY
            Id", $result);
    }
}
