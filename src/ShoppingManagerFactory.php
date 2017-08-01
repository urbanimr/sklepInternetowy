<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/OrdersGateway.php';
require_once __DIR__ . '/../src/CarriersGateway.php';
require_once __DIR__ . '/../src/OrderStatusesGateway.php';
require_once __DIR__ . '/../src/OrderProductsGateway.php';
require_once __DIR__ . '/../src/ShoppingManager.php';
require_once __DIR__ . '/../src/CatalogProductsGateway.php';

abstract class ShoppingManagerFactory
{    
    static public function create(PDO $conn)
    {
        $carriersGateway = new CarriersGateway($conn);
        $orderStatusesGateway = new OrderStatusesGateway($conn);
        $orderProductsGateway = new OrderProductsGateway($conn);
        $catalogProductsGateway = new CatalogProductsGateway($conn);
        
        $ordersGateway = new OrdersGateway(
            $conn,
            $carriersGateway,
            $orderStatusesGateway,
            $orderProductsGateway,
            $catalogProductsGateway
        );
        
        $shoppingManager = new ShoppingManager(
            $ordersGateway,
            $carriersGateway,
            $orderStatusesGateway,
            $catalogProductsGateway
        );
        
        return $shoppingManager;
    }
}