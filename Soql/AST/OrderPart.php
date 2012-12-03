<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class OrderPart
{
    /**
     * @var array
     */
    private $orderFields;

    public function __construct()
    {
        $this->orderFields = array();
    }

    /**
     * @param OrderByFieldInterface $field
     * @return void
     */
    public function addOrderField(OrderByFieldInterface $field)
    {
        $this->orderFields[] = $field;
    }

    /**
     * @param array $fields<OrderByFieldInterface>
     */
    public function addOrderFields(array $fields)
    {
        foreach($fields AS $field)
        {
            $this->addOrderField($field);
        }
    }

    /**
     * @return array
     */
    public function getOrderFields()
    {
        return $this->orderFields;
    }
}
