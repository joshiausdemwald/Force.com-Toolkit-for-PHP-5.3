<?php
namespace Codemitte\Sfdc\Soql\AST;

class OrderPart extends AbstractSoqlPart
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
     * @param \Codemitte\Sfdc\Soql\AST\SortableInterface $field
     * @return void
     */
    public function addOrderField(SortableInterface $field)
    {
        $this->orderFields[] = $field;
    }

    /**
     * @param array $fields<SortableInterface>
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
