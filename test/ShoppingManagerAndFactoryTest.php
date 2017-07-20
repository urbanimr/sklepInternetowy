<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/ShoppingManagerFactory.php';
require_once __DIR__ . '/../src/ShoppingManager.php';
require_once __DIR__ . '/../src/User.php';

class ShoppingManagerAndFactoryTest extends PHPUnit_Extensions_Database_TestCase
{
    private $connection;
    private $manager;
    private $user;
    
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
        
        $this->manager = ShoppingManagerFactory::create(
            $this->connection
        );
        
        $this->user = $this->createMock(User::class);
        $this->user->method('getId')->willReturn(1);
        $this->user->method('getBillingAddressId')->willReturn(1);
        $this->user->method('getShippingAddressId')->willReturn(2);
    }
    
    public function testLoadOrCreateBasketByUserWhenBasketExistsLoadsTheBasket()
    {
        $existingBasketStatus = [
            'id' => 1,
            'order_id' => 1
        ];
        
        $basket = $this->manager->loadOrCreateBasketByUser($this->user);
        $this->assertInstanceOf(Order::class, $basket);
        $this->assertEquals($existingBasketStatus['order_id'], $basket->getId());
    }
    
    public function testLoadOrCreateBasketByUserWhenBasketNotExistsCreatesBasket()
    {
        $userWithoutBasketId = 4;
       
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userWithoutBasketId);
        $user->method('getBillingAddressId')->willReturn(1);
        $user->method('getShippingAddressId')->willReturn(2);
        
        $basket = $this->manager->loadOrCreateBasketByUser($user);
        
        $this->assertInstanceOf(Order::class, $basket);
        $this->assertEquals(
            $user->getId(),
            $basket->getUserId()
        );
        $this->assertEquals(-1, $basket->getId());

        $statuses = $basket->getStatuses();
        $this->assertCount(1, $statuses);
        $status = $statuses[0];
        $this->assertInstanceOf(OrderStatus::class, $status);
        $this->assertEquals(-1, $status->getId());
        $this->assertEquals(OrderStatus::STATUS_BASKET, $status->getStatusId());
        
        $products = $basket->getOrderProducts();
        $this->assertCount(0, $products);
    }
}
