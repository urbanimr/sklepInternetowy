<?php
require_once __DIR__ . '/../src/TableGateway.php';
require_once __DIR__ . '/../src/OrderStatus.php';

class OrderStatusesGateway extends TableGateway
{
    public function __construct(PDO $conn) {
        $table = 'order_statuses';
        parent::__construct($conn, $table);
    }
    
    public function loadStatusesByOrderId($orderId)
    {
        $joinedTables = [
            'statuses' => [
                'joinColumns' => ['status_id', 'id'],
                'selectedColumns' => ['status_name']
            ]
        ];
        
        $args = [
            'whereColumn' => 'order_id',
            'whereValue' => $orderId,
            'joinedTables' => $joinedTables,
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'date',
            'isOrderAsc' => true
        ];
        return $this->loadItemsByColumn(
            $args['whereColumn'],
            $args['whereValue'],
            $args['joinedTables'],
            $args['limit'],
            $args['offset'],
            $args['orderBy'],
            $args['isOrderAsc']
        );
    }
    
    public function loadBasketStatusByUserId(int $userId)
    {
        $joinedTables = [
            'statuses' => [
                'joinColumns' => ['status_id', 'id'],
                'selectedColumns' => ['status_name']
            ],
            'orders' => [
                'joinColumns' => ['order_id', 'id'],
                'selectedColumns' => ['user_id']
            ]
        ];
        
        $args = [
            'whereColumn' => 'user_id',
            'whereValue' => $userId,
            'joinedTables' => $joinedTables,
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'status_id',
            'isOrderAsc' => true
        ];
        $allStatuses = $this->loadItemsByColumn(
            $args['whereColumn'],
            $args['whereValue'],
            $args['joinedTables'],
            $args['limit'],
            $args['offset'],
            $args['orderBy'],
            $args['isOrderAsc']
        );
        
        if (empty($allStatuses)) {
            return false;
        }
        
        if ($allStatuses[0]->getStatusId() != OrderStatus::STATUS_BASKET) {
            return false;
        }
        
        return $allStatuses[0];
    }
    
    protected function createItemFromRow($row)
    {
        $newItem = new OrderStatus();
        $newItem->importArray($row);
        return $newItem;
    }
    
    public function save(OrderStatus $orderStatus)
    {
        if ($orderStatus->getId() != -1) {
            return false;
        }
        
        return $this->insertItem($orderStatus);
    }
    
    public function delete(OrderStatus $orderStatus)
    {
        if ($orderStatus->getId() == -1) {
            return false;
        }
        return $this->deleteItem($orderStatus);
    }
}