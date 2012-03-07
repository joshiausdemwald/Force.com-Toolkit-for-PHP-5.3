<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class DescribeLayoutButtonSection implements ClassInterface
{

    /**
     * @var DescribeLayoutButton $detailButtons
     */
    private $detailButtons;

    /**
     *
     * @param DescribeLayoutButton $detailButtons
     */
    public function __construct($detailButtons)
    {
        $this->detailButtons = $detailButtons;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\DescribeLayoutButton
     */
    public function getDetailButtons()
    {
        return $this->detailButtons;
    }

}
