<?php
require_once __DIR__ . '/../src/TableGateway.php';
require_once __DIR__ . '/../src/CatalogProductsGateway.php';

class OrdersGateway extends TableGateway
{
    private $carriersGateway;
    private $orderStatusesGateway;
    private $orderProductsGateway;
    private $catalogProductsGateway;
    
    public function __construct(
        PDO $conn,
        CarriersGateway $carriersGateway,
        OrderStatusesGateway $orderStatusesGateway,
        OrderProductsGateway $orderProductsGateway,
        CatalogProductsGateway $catalogProductsGateway
    ) {
        $table = 'orders';
        $this->carriersGateway = $carriersGateway;
        $this->orderProductsGateway = $orderProductsGateway;
        $this->orderStatusesGateway = $orderStatusesGateway;
        $this->catalogProductsGateway = $catalogProductsGateway;
        parent::__construct($conn, $table);
    }
    
    public function loadOrderByColumn(string $column, $value)
    {
        $joinedTables = [
            'carriers' => [
                'joinColumns' => ['carrier_id', 'id'],
                'selectedColumns' => ['carrier_name']
            ],
            'payment_methods' => [
                'joinColumns' => ['payment_id', 'id'],
                'selectedColumns' => ['payment_name']
            ]
        ];
        
        $args = [
            'whereColumn' => $column,
            'whereValue' => $value,
            'joinedTables' => $joinedTables,
            'limit' => 1,
            'offset' => 0,
            'orderBy' => 'id',
            'isOrderAsc' => false
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
    
    public function loadRecentOrders(int $limit, int $offset = 0)
    {
        $joinedTables = [
            'carriers' => [
                'joinColumns' => ['carrier_id', 'id'],
                'selectedColumns' => ['carrier_name']
            ],
            'payment_methods' => [
                'joinColumns' => ['payment_id', 'id'],
                'selectedColumns' => ['payment_name']
            ],
            'order_statuses' => [
                'joinColumns' => ['id', 'order_id'],
                'selectedColumns' => ['status_id', 'date']
            ]
        ];
        
        $args = [
            'whereColumn' => 'status_id',
            'whereValue' => 2,
            'joinedTables' => $joinedTables,
            'limit' => $limit,
            'offset' => $offset,
            'orderBy' => 'date',
            'isOrderAsc' => false
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
    
    public function loadSubmittedOrdersByUser(User $user)
    {
        $userId = $user->getId();
        $completeQuery = 'SELECT orders.* FROM orders'
            . ' '
            . 'LEFT JOIN users ON orders.user_id = users.id'
            . ' '
            . 'LEFT JOIN order_statuses ON orders.id = order_statuses.order_id'
            . ' '
            . "WHERE user_id = $userId"
            . ' '
            . 'AND order_statuses.status_id = 2'
            . ' '
            . 'ORDER BY date ASC';

        $returnArray = [];
        
        $stmt = $this->conn->prepare($completeQuery);
        $result = $stmt->execute();
        if ($result == true && $stmt->rowCount() > 0) {
            foreach ($stmt as $row) {
                $loadedItem = $this->createItemFromRow($row);
                $returnArray[] = $loadedItem;
            }            
        }
        
        return $returnArray;  
    }

    protected function createItemFromRow($row)
    {
        $order = new Order($this->carriersGateway, $this->catalogProductsGateway);
        $order->importArray($row);
        $this->loadStatusesFor($order);
        $this->loadProductsFor($order);
        $order->setCarrier($order->getCarrier()); //a way to update shipping cost for baskets only
        
        return $order;
    }
    
    private function loadStatusesFor(Order $order)
    {
        $statuses = $this->orderStatusesGateway->loadStatusesByOrderId($order->getId());
        foreach($statuses as $status) {
            $order->addStatus($status);
        }
    }
    
    private function loadProductsFor(Order $order)
    {
        $products = $this->orderProductsGateway->loadProductsByOrderId($order->getId());
        
        if (empty($products)) {
            return;
        }
        
        if ($order->getLastStatus()->getStatusId() === OrderStatus::STATUS_BASKET) {
            foreach ($products as $product) {
                $actualProductId = $product->getProductId();
                $actualProduct = Product::showProductById($this->conn, $actualProductId);
                $currentPrice = $actualProduct->getPrice();
                $product->setPrice($currentPrice);
            }
        }
        
        $order->setProductsWithOldPrices($products);
    }
    
    public function save(Order $order)
    {
        if ($order->getId() == -1) {
            $orderSavedSuccessfully = $this->insertItem($order);
        } else {
            $orderSavedSuccessfully = $this->updateItem($order);
        }
        
        if (false === $orderSavedSuccessfully) {
            return false;
        }
        
        $this->saveStatusesFor($order);
        $this->saveProductsFor($order);

        return true;
    }
    
    private function saveStatusesFor(Order $order)
    {
        foreach ($order->getStatuses() as $status) {
            if ($status->getId() != -1) {
                continue; //you can only insert new status, not modify old one
            }
            
            $status->setOrderId($order->getId());
            $statusSavedSuccessfully = $this->orderStatusesGateway->save($status);
            //todo: error when saving status should break the loop, rollback every db action and return false
        }
    }
    
    private function saveProductsFor(Order $order)
    {
        foreach ($order->getOrderProducts() as $product) {
            $product->setOrderId($order->getId());
            $productSavedSuccessfully = $this->orderProductsGateway->save($product);
            //todo: error when saving product should break the loop, rollback every db action and return false
        }
    }
}