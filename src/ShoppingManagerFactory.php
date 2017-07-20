<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/OrdersGateway.php';
require_once __DIR__ . '/../src/CarriersGateway.php';
require_once __DIR__ . '/../src/OrderStatusesGateway.php';
require_once __DIR__ . '/../src/OrderProductsGateway.php';
require_once __DIR__ . '/../src/ShoppingManager.php';

abstract class ShoppingManagerFactory
{    
    static public function create(PDO $conn)
    {
        $carriersGateway = new CarriersGateway($conn);
        $orderStatusesGateway = new OrderStatusesGateway($conn);
        $orderProductsGateway = new OrderProductsGateway($conn);
        
        $ordersGateway = new OrdersGateway(
            $conn,
            $carriersGateway,
            $orderStatusesGateway,
            $orderProductsGateway
        );
        
        $shoppingManager = new ShoppingManager(
            $ordersGateway,
            $carriersGateway,
            $orderStatusesGateway
        );
        
        return $shoppingManager;
    }
}