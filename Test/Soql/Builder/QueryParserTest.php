<?php
namespace Codemitte\ForceToolkit\Test\Soql\Builder;

use
    Codemitte\ForceToolkit\Soap\Mapping\Base\login,
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Client\PartnerClient,
    Codemitte\ForceToolkit\Soql\Builder\QueryBuilder,
    Codemitte\ForceToolkit\Soql\Parser\QueryParser,
    Codemitte\ForceToolkit\Soql\Tokenizer\Tokenizer,
    Codemitte\ForceToolkit\Soql\Renderer\QueryRenderer,
    Codemitte\ForceToolkit\Soql\Type\TypeFactory,
    Codemitte\ForceToolkit\Soql\Builder\ExpressionBuilderInterface AS Expr
;

/**
 * @group Soql
 */
class QueryParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PartnerClient
     */
    private static $client;

    /**
     * @var SfdcConnection
     */
    private static $connection;

    public static function setUpBeforeClass()
    {
        self::setUpConnection();
        self::setUpClient();
    }

    private static function setUpConnection()
    {
        $credentials = new login(SFDC_USERNAME, SFDC_PASSWORD);

        $wsdl = __DIR__ . '/../../fixtures/partner.wsdl.xml';

        $serviceLocation = SFDC_SERVICE_LOCATION ? SFDC_SERVICE_LOCATION : null;

        self::$connection = new SfdcConnection($credentials, $wsdl, $serviceLocation, array(), true);
    }

    private static function setUpClient()
    {
        self::$connection->login();

        self::$client = new PartnerClient(self::$connection);
    }

    private function newBuilder()
    {
        return new QueryBuilder(self::$client, new QueryParser(new Tokenizer()), new QueryRenderer(new TypeFactory()));
    }

    public function testBuilder()
    {
        $builder = $this->newBuilder();

        $res = $builder
            ->select('Id')
            ->from('Account')
            ->where($builder
                ->whereExpr()
                ->xpr('Name', Expr::OP_NEQ, 'NULL')
                ->andXpr('AccountNumber', Expr::OP_NEQ, 'NULL')
            )
            ->limit(1)
            ->fetch();

    }
}
