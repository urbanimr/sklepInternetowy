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
        $completeQuery = 'SELECT order_statuses.*, statuses.status_name FROM order_statuses'
            . ' '
            . 'LEFT JOIN statuses ON order_statuses.status_id = statuses.id'
            . ' '
            . 'LEFT JOIN orders ON order_statuses.order_id = orders.id'
            . ' '
            . "WHERE orders.user_id = $userId"
            . ' '
            . 'ORDER BY order_statuses.order_id DESC'
            . ', '
            . 'order_statuses.date DESC'
            . ' '
            . 'LIMIT 1';

        $stmt = $this->conn->prepare($completeQuery);
        $result = $stmt->execute();
        
        if ($result != true || $stmt->rowCount() != 1) {
            return null;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastStatusIsNotBasket = $row['status_id'] != OrderStatus::STATUS_BASKET;
        if ($lastStatusIsNotBasket) {
            return null;
        }
        
        $loadedItem = $this->createItemFromRow($row);
        return $loadedItem;  
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