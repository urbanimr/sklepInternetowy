<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Order.php';
require_once __DIR__ . '/../src/OrdersGateway.php';
require_once __DIR__ . '/../src/CarriersGateway.php';
require_once __DIR__ . '/../src/OrderProductsGateway.php';
require_once __DIR__ . '/../src/OrderStatusesGateway.php';
require_once __DIR__ . '/../src/InputValidator.php';
require_once __DIR__ . '/../src/Carrier.php';
require_once __DIR__ . '/../src/Product.php';
require_once __DIR__ . '/../src/User.php';

class OrdersGatewayTest extends PHPUnit_Extensions_Database_TestCase
{
    private $connection;
    private $gateway;
    private $carriersGateway;
    private $orderOneVals;
    private $orderOneProducts;
    
    protected function getConnection()
    {
        $conn = new PDO(
            'mysql:host=localhost;dbname=store;charset=UTF8',
            'root',
            'coderslab'
        );
        return $this->createDefaultDBConnection($conn, 'store');
    }

    protected function getDataSet()
    {
        $dataXML = $this->createXMLDataSet(__DIR__ . '/testdb.xml');
        return $dataXML;
    }
    
    protected function setUp()
    {
        parent::setUp();
        $this->connection = new PDO(
            'mysql:host=localhost;dbname=store;charset=UTF8',
            'root',
            'coderslab'
        );
        
        $this->carriersGateway = new CarriersGateway($this->connection);
        
        $this->gateway = new OrdersGateway(
            $this->connection,
            $this->carriersGateway,
            new OrderStatusesGateway($this->connection),
            new OrderProductsGateway($this->connection)
        );

        $this->orderOneVals = [
            'id' => 1,
            'user_id' => 1,
            'billing_address' => 1,
            'shipping_address' => 2,
            'carrier_id' => Carrier::CARRIER_POCZTEX,
            'payment_id' => Order::PAYMENT_TRANSFER,
            'comment' => 'Towary potrzebne na wczoraj',
            'shipping_cost' => 20.00,
            'total_amount' => 46.95
        ];
        $this->orderOneProducts = [
            [
                'id' => 1,
                'qty' => 2
            ],
            [
                'id' => 2,
                'qty' => 5
            ],
            [
                'id' => 3,
                'qty' => 1
            ]
        ];
    }
    
    public function testLoadOrderByColumn()
    {
        $order = $this->gateway->loadOrderByColumn('id', 1);
        
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($this->orderOneVals['id'], $order->getId());
        $this->assertEquals($this->orderOneVals['user_id'], $order->getUserId());
        $this->assertEquals($this->orderOneVals['billing_address'], $order->getBillingAddress());
        $this->assertEquals($this->orderOneVals['shipping_address'], $order->getShippingAddress());
        $this->assertEquals($this->orderOneVals['carrier_id'], $order->getCarrier());
        $this->assertEquals($this->orderOneVals['payment_id'], $order->getPayment());
        $this->assertEquals($this->orderOneVals['comment'], $order->getComment());
        $this->assertEquals($this->orderOneVals['shipping_cost'], $order->getShippingCost());
        $this->assertEquals($this->orderOneVals['total_amount'], $order->getTotalAmount());
    }
    
    public function testLoadOrderByColumnLoadsAndAddsStatuses()
    {
        $order = $this->gateway->loadOrderByColumn('id', 1);
        $statuses = $order->getStatuses();
        $this->assertCount(3, $statuses);
        foreach ($statuses as $status) {
            $this->assertInstanceOf(OrderStatus::class, $status);
            $this->assertEquals($order->getId(), $status->getOrderId());
        }
        $firstStatus = $statuses[0];
        $this->assertEquals(OrderStatus::STATUS_BASKET, $firstStatus->getStatusId());
        $this->assertEquals(OrderStatus::STATUS_NAME_BASKET, $firstStatus->getStatusName());
    }
    
    public function testLoadOrderByColumnLoadsAndAddsProducts()
    {
        $order = $this->gateway->loadOrderByColumn('id', 1);
        $products = $order->getOrderProducts();
        $this->assertCount(3, $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(OrderProduct::class, $product);
            $this->assertEquals($order->getId(), $product->getOrderId());
        }
        $firstProduct = $products[0];
        $this->assertEquals(2, $firstProduct->getQuantity());
        $this->assertEquals(11.40, $firstProduct->getPrice());
        
        $this->assertEquals(8, $order->countProducts());
        
        $shippingCost = $order->getShippingCost();
        $totalAmount = $order->getTotalAmount();
        $totalAmountIncludesLoadedProducts = $totalAmount > $shippingCost;
        $this->assertTrue($totalAmountIncludesLoadedProducts);
    }
    
    public function testLoadOrderWithBasketStatusLoadsCurrentPricesAndShippingCost()
    {        
        // order 2
        //- basket
        //- carrier 3, shippingCost: 20.00 (now should be: 18.50)
        //- 1 x product 3, sum of products: 11.40 (now should be 12.00)
        //- total amount: 31,40 (now should be 30.50)
        
        $orderId = 2;        
        $order = $this->gateway->loadOrderByColumn('id', $orderId);
        
        //make sure order2 last status is basket
        $this->assertEquals(
            $order->getLastStatus()->getStatusId(),
            OrderStatus::STATUS_BASKET
        );
        
        $productId = 3;
        $productNow = Product::showProductById($this->connection, $productId);
        $productFromOrder = $order->getOrderProductById($productId);
        $this->assertEquals($productNow->getPrice(), $productFromOrder->getPrice());
        
        $carrierId = $order->getCarrier();
        $carrierPriceNow = $this->carriersGateway->loadCarrierById($carrierId)->getPrice();
        $shippingCostFromOrder = $order->getShippingCost();
        $this->assertEquals($carrierPriceNow, $shippingCostFromOrder);
        
        $this->assertEquals(30.50, $order->getTotalAmount());
    }
    
    public function testLoadSubmittedOrderLoadsOldPricesAndShippingCost()
    {
        // order 1
        //- not basket
        //- carrier 3, shipping cost 20.00 (now would be: 18.50)
        //- sum of products: 38.35 (now would be different)
        //- total amount: 46.95 (now would be: 48.90)
        
        $orderId = 1;
        $order1 = $this->gateway->loadOrderByColumn('id', $orderId);
        
        //make sure order1 last status is not basket
        $this->assertNotEquals(
            $order1->getLastStatus()->getStatusId(),
            OrderStatus::STATUS_BASKET
        );
        
        $productId = 3;
        $productNow = Product::showProductById($this->connection, $productId);
        $productFromOrder = $order1->getOrderProductById($productId);
        $this->assertNotEquals($productNow->getPrice(), $productFromOrder->getPrice());
        
        $carrierId = $order1->getCarrier();
        $carrierPriceNow = $this->carriersGateway->loadCarrierById($carrierId)->getPrice();
        $shippingCostFromOrder = $order1->getShippingCost();
        $this->assertNotEquals($carrierPriceNow, $shippingCostFromOrder);
        
        $this->assertEquals(46.95, $order1->getTotalAmount());
    }
   
    public function testSaveWithNonPersistedOrderInsertsRowChangesIdReturnsTrue()
    {
        $newOrder = new Order($this->carriersGateway);
        $values = [
            'user_id' => 2,
            'billing_address' => 2,
            'shipping_address' => 1,
            'carrier_id' => Carrier::CARRIER_DHL,
            'payment_id' => Order::PAYMENT_CASH,
            'comment' => 'Nowa uwaga',
            'shipping_cost' => 14.00,
            'total_amount' => 14.00
        ];
        $newOrder->importArray($values);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('orders');
        $result = $this->gateway->save($newOrder);
        $this->assertTrue($result);
        $this->assertNotEquals(-1, $newOrder->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('orders');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testSaveWithPersistedOrderOnlyUpdatesRowAndReturnsTrue()
    {
        $orderId = 1;
        $order = $this->gateway->loadOrderByColumn('id', $orderId);
        $rowCountBeforeUpdate = $this->getConnection()->getRowCount('orders');
        $newValues = [
            'user_id' => 2,
            'billing_address' => 2,
            'shipping_address' => 1,
            'carrier_id' => Carrier::CARRIER_DHL,
            'payment_id' => Order::PAYMENT_CASH,
            'comment' => 'Nowa uwaga',
            'shipping_cost' => 14.00,
            'total_amount' => 14.00
        ];
        $order->importArray($newValues);
        $result = $this->gateway->save($order);
        $this->assertTrue($result);
        $this->assertEquals($orderId, $order->getId());
        $rowCountAfterUpdate = $this->getConnection()->getRowCount('orders');
        $this->assertEquals($rowCountBeforeUpdate, $rowCountAfterUpdate);
        $order = null;
        $order = $this->gateway->loadOrderByColumn('id', $orderId);
        $this->assertEquals($newValues['user_id'], $order->getUserId());
        $this->assertEquals($newValues['billing_address'], $order->getBillingAddress());
        $this->assertEquals($newValues['shipping_address'], $order->getShippingAddress());
        $this->assertEquals($newValues['carrier_id'], $order->getCarrier());
        $this->assertEquals($newValues['payment_id'], $order->getPayment());
        $this->assertEquals($newValues['comment'], $order->getComment());
        $this->assertEquals($newValues['shipping_cost'], $order->getShippingCost());
        $this->assertEquals($newValues['total_amount'], $order->getTotalAmount());
    }
    
    public function testSaveUpdatesOrderIdPropertyOfStatusesAndCallsSaveOnEveryStatus()
    {
        $newOrder = new Order($this->carriersGateway);
        $orderValues = [
            'user_id' => 2,
            'billing_address' => 2,
            'shipping_address' => 1,
            'carrier_id' => Carrier::CARRIER_DHL,
            'payment_id' => Order::PAYMENT_CASH,
            'comment' => 'Nowa uwaga',
            'shipping_cost' => 14.00,
            'total_amount' => 14.00
        ];
        $newOrder->importArray($orderValues);
        
        $status1 = new OrderStatus();
        $status1values = [
            'status_id' => OrderStatus::STATUS_BASKET,
            'status_name' => 'Basket',
            'order_id' => $newOrder->getId(),
            'date' => date('Y-m-d H:i:s')
        ];
        $status1->importArray($status1values);
        $newOrder->addStatus($status1);
        
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('order_statuses');
        $result = $this->gateway->save($newOrder);
        
        $this->assertTrue($result);
        $this->assertNotEquals(-1, $status1->getId());
        $this->assertEquals($newOrder->getId(), $status1->getOrderId());
        
        $rowCountAfterInsert = $this->getConnection()->getRowCount('order_statuses');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testSaveUpdatesOrderIdPropertyOfProductsAndCallsSaveOnEveryProduct()
    {
        $newOrder = new Order($this->carriersGateway);
        $orderValues = [
            'user_id' => 2,
            'billing_address' => 2,
            'shipping_address' => 1,
            'carrier_id' => Carrier::CARRIER_DHL,
            'payment_id' => Order::PAYMENT_CASH,
            'comment' => 'Nowa uwaga',
            'shipping_cost' => 14.00,
            'total_amount' => 14.00
        ];
        $newOrder->importArray($orderValues);
        
        $productId = 3;
        $actualProduct = Product::showProductById($this->connection, $productId);
        $newOrder->addProducts($actualProduct, 2);
        $productFromOrder = $newOrder->getOrderProductById($productId);

        
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('order_products');
        $result = $this->gateway->save($newOrder);
        
        $this->assertTrue($result);
        $this->assertNotEquals(-1, $productFromOrder->getId());
        $this->assertEquals($newOrder->getId(), $productFromOrder->getOrderId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('order_products');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testLoadSubmittedOrdersByUserLoadCorrectOrdersSortedBySubmitDateAsc()
    {
        $userId = 1;
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);
        $user->method('getBillingAddressId')->willReturn(1);
        $user->method('getShippingAddressId')->willReturn(2);
        
        $orders = $this->gateway->loadSubmittedOrdersByUser($user);
        
        $this->assertInternalType('array', $orders);
        $submitDates = [];
        foreach ($orders as $order) {
            $this->assertInstanceOf(Order::class, $order);
            $this->assertEquals($userId, $order->getUserId());
            
            $lastStatusId = $order->getLastStatus()->getStatusId();
            $this->assertNotEquals(OrderStatus::STATUS_BASKET, $lastStatusId);
            
            $submitDates[] = strtotime($order->getStatuses()[1]->getDate());
        }
        
        $submitDatesInExpectedOrder = $submitDates;
        sort($submitDatesInExpectedOrder);
        $this->assertEquals($submitDatesInExpectedOrder, $submitDates);
    }
    
    public function testLoadRecentOrdersLoadCorrectSetOfOrdersSortedBySubmitDateDesc()
    {
        $limit = 2;
        $ordersWithoutOffset = $this->gateway->loadRecentOrders($limit, 0);
        
        $this->assertInternalType('array', $ordersWithoutOffset);
        $this->assertCount(2, $ordersWithoutOffset);
        
        $ordersWithOffset = $this->gateway->loadRecentOrders($limit, 1);
        $this->assertEquals(
            $ordersWithoutOffset[1]->getId(),
            $ordersWithOffset[0]->getId()
        );
        
        $submitDates = [];
        foreach ($ordersWithoutOffset as $order) {
            $this->assertInstanceOf(Order::class, $order);
            
            $lastStatusId = $order->getLastStatus()->getStatusId();
            $this->assertNotEquals(OrderStatus::STATUS_BASKET, $lastStatusId);
            
            $submitDates[] = strtotime($order->getStatuses()[1]->getDate());
        }
        
        $submitDatesInExpectedOrder = $submitDates;
        rsort($submitDatesInExpectedOrder);
        $this->assertEquals($submitDatesInExpectedOrder, $submitDates);
    }
}
