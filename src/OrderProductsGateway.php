<?php
require_once __DIR__ . '/../src/TableGateway.php';
require_once __DIR__ . '/../src/OrderProduct.php';
require_once __DIR__ . '/../src/Product.php';

class OrderProductsGateway extends TableGateway
{
    public function __construct(PDO $conn) {
        $table = 'order_products';
        parent::__construct($conn, $table);
    }
    
    public function loadProductsByOrderId($orderId)
    {
        $joinedTables = [];
        
        $args = [
            'whereColumn' => 'order_id',
            'whereValue' => $orderId,
            'joinedTables' => $joinedTables,
            'limit' => 1000,
            'offset' => 0,
            'orderBy' => 'id',
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
    
    protected function createItemFromRow($row)
    {
        $newItem = new OrderProduct();
        $newItem->importArray($row);
        
        $catalogProduct = Product::showProductById($this->conn, $newItem->getProductId());
        if ($catalogProduct instanceof Product) {
            $newItem->setCatalogProduct($catalogProduct);
        }
        
        return $newItem;
    }
    
    public function save(OrderProduct $orderProduct)
    {
        if ($orderProduct->getId() == -1) {
            return $this->saveNewItem($orderProduct);
        }
        
        return $this->saveOldItem($orderProduct);
    }
    
    private function saveNewItem(OrderProduct $orderProduct)
    {
        if ($orderProduct->getQuantity() <= 0) {
            return false;
        }
        
        return $this->insertItem($orderProduct);
    }
    
    private function saveOldItem(OrderProduct $orderProduct)
    {
        if ($orderProduct->getQuantity() <= 0) {
            return $this->delete($orderProduct);
        }
        
        return $this->updateItem($orderProduct);
    }
    
    public function delete(OrderProduct $orderProduct)
    {
        if ($orderProduct->getId() == -1) {
            return false;
        }
        return $this->deleteItem($orderProduct);
    }
}