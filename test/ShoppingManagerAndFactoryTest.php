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
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD']
        );
        return $this->createDefaultDBConnection($conn, $GLOBALS['DB_DBNAME']);
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
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD']
        );
        
        $this->manager = ShoppingManagerFactory::create(
            $this->connection
        );
        
        $this->user = $this->createMock(User::class);
        $this->user->method('getId')->willReturn(1);
        $this->user->method('getBillingAddressId')->willReturn(1);
        $this->user->method('getShippingAddressId')->willReturn(2);
    }

    public function testLoadOrCreateBasketWhenBasketExistsLoadsTheBasket()
    {
        //only the last basket status is a real basket as it is the last status for this order
        //notice that the basket status is not the last recorded status for this user
        $userOneBaskets = [
            [
                'id' => 1,
                'order_id' => 1
            ],
            [
                'id' => 4,
                'order_id' => 2
            ],
            [
                'id' => 6,
                'order_id' => 3
            ]
        ];
        
        $basket = $this->manager->loadOrCreateBasketByUser($this->user);
        
        $rowCountBefore = $this->getConnection()->getRowCount('order_statuses');
        $this->manager->save($basket);
        $rowCountAfter = $this->getConnection()->getRowCount('order_statuses');
        $rowCountDifference = $rowCountBefore - $rowCountAfter;
        $this->assertEquals(0, $rowCountDifference);
        
        $this->assertInstanceOf(Order::class, $basket);
        $this->assertEquals($userOneBaskets[2]['order_id'], $basket->getId());
        $this->assertEquals($userOneBaskets[2]['id'], $basket->getLastStatus()->getId());
    }
    
    public function testLoadOrCreateBasketByUserWhenBasketNotExistsCreatesBasket()
    {
        $idOfUserWithOldBasketStatusButNoActualBasket = 2; 
       
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($idOfUserWithOldBasketStatusButNoActualBasket);
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
