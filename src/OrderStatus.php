<?php
require_once __DIR__ . '/../src/TableRow.php';

class OrderStatus implements TableRow, JsonSerializable
{
    const STATUS_BASKET = 1;
    const STATUS_SUBMITTED = 2;
    const STATUS_PAID = 3;
    const STATUS_SHIPPED = 4;
    const STATUS_DELIVERED = 5;
    const STATUS_CANCELED = 6;
    
    const STATUS_NAME_UNKNOWNED = '';
    const STATUS_NAME_BASKET = 'Basket';
    
    private $id;
    private $statusId;
    private $statusName;
    private $orderId;
    private $date;
    
    public function __construct() {
        $this->id = -1;
        $this->statusId = -1;
        $this->statusName = '';
        $this->orderId = -1;
        $this->date = '0000-00-00 00:00:00';
    }
    
    public function createNewStatus(int $statusId)
    {
        $values = [
            'id' => -1,
            'status_id' => $statusId,
            'status_name' => '',
            'order_id' => $this->getOrderId(),
            'date' => date('Y-m-d H:i:s')
        ];
        $status = new OrderStatus();
        $status->importArray($values);
        return $status;
    }
    
    public function jsonSerialize()
    {
        $array = $this->exportArray();
        $array['id'] = $this->getId();
        return $array;
    }
    
    public function importArray(array $data)
    {
        $this->setId(
            isset($data['id'])
                ? $data['id']
                : $this->id
        );
        $this->setStatusId(
            isset($data['status_id'])
                ? $data['status_id']
                : $this->statusId
        );
        $this->setStatusName(
            isset($data['status_name'])
                ? $data['status_name']
                : $this->statusName
        );
        $this->setOrderId(
            isset($data['order_id'])
                ? $data['order_id']
                : $this->orderId
        );
        $this->setDate(
            isset($data['date'])
                ? $data['date']
                : $this->date
        );
    }
    
    public function exportArray()
    {
        return [
            'status_id' => $this->getStatusId(),
            'order_id' => $this->getOrderId(),
            'date' => $this->getDate()
        ];
    }
    
    /**
     * @param InputValidator $validator
     * @param array $data e.g. ['name' => 'Jan Kowalski']
     * @param array $requiredFields e.g. ['name', 'email', 'dateCreated']
     * @return boolean True if required data is present and all data is valid
     */
    public static function validate(
        InputValidator $validator,
        array $data,
        array $requiredFields
    ) {
        for ($i = 0; $i < count($requiredFields); $i++) {
            if (!isset($data[$requiredFields[$i]])) {
                return false;
            }
        }
        
        $validator->clear();
        $validator->addInput($data);
        
        if (isset($data['id'])) {
            $validator->addValidations([
                'id' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        if (isset($data['status_id'])) {
            $validator->addValidations([
                'status_id' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }

        if (isset($data['status_name'])) {
            $validator->addValidations([
                'status_name' => [
                    ['notEmpty'],
                    ['shorterThan',20]
                ]
            ]);
        }
        
        if (isset($data['order_id'])) {
            $validator->addValidations([
                'order_id' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        if (isset($data['date'])) {
            $validator->addValidations([
                'date' => [
                    ['notEmpty'],
                    ['sqlDatePattern']
                ]
            ]);
        }
        
        return $validator->validate();
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setStatusId(int $statusId)
    {
        $this->statusId = $statusId;
    }

    public function setStatusName(string $statusName)
    {
        $this->statusName = $statusName;
    }

    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function setDate(string $date)
    {
        $this->date = $date;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getStatusId()
    {
        return $this->statusId;
    }

    public function getStatusName()
    {
        return $this->statusName;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getDate()
    {
        return $this->date;
    }
}