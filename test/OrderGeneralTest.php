<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Order.php';
require_once __DIR__ . '/../src/Carrier.php';
require_once __DIR__ . '/../src/CarriersGateway.php';
require_once __DIR__ . '/../src/InputValidator.php';
require_once __DIR__ . '/../src/CatalogProductsGateway.php';

class OrderGeneralTest extends PHPUnit_Framework_TestCase
{
    private $carriersGateway;
    private $catalogProductsGateway;
    private $connection;
    
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
        $this->catalogProductsGateway = new CatalogProductsGateway($this->connection);
    }
    
    public function testConstructSetsDefaultEmptyValues()
    {
        $basket = new Order($this->carriersGateway, $this->catalogProductsGateway);
        $this->assertEquals(-1, $basket->getId());
        $this->assertEquals(-1, $basket->getUserId());
        $this->assertEquals(-1, $basket->getBillingAddress());
        $this->assertEquals(-1, $basket->getShippingAddress());
        $this->assertEquals(Carrier::CARRIER_PICKUP, $basket->getCarrier());
        $this->assertEquals(Order::PAYMENT_CASH, $basket->getPayment());
        $this->assertEquals('', $basket->getComment());
        $this->assertEquals(0.00, $basket->getShippingCost());
        $this->assertEquals(0.00, $basket->getTotalAmount());
        $this->assertInternalType('array', $basket->getOrderProducts());
        $this->assertCount(0, $basket->getOrderProducts());
        $this->assertInternalType('array', $basket->getStatuses());
        $this->assertCount(0, $basket->getStatuses());
    }
    
    public function testSettersSetRightValues()
    {
        $newValues = [
            'id' => 2,
            'user_id' => 3,
            'billing_address' => 225,
            'shipping_address' => 285,
            'carrier' => Carrier::CARRIER_PICKUP,
            'payment' => Order::PAYMENT_CASH,
            'comment' => 'Towary potrzebne na wczoraj',
            'shipping_cost' => 14.00,
            'total_amount' => 65.65
        ];
        $basket = new Order($this->carriersGateway, $this->catalogProductsGateway);
        $basket->setId($newValues['id']);
        $basket->setUserId($newValues['user_id']);
        $basket->setBillingAddress($newValues['billing_address']);
        $basket->setShippingAddress($newValues['shipping_address']);
        $basket->setCarrier($newValues['carrier']);
        $basket->setPayment($newValues['payment']);
        $basket->setComment($newValues['comment']);
        $basket->setShippingCost($newValues['shipping_cost']);
        $basket->setTotalAmount($newValues['total_amount']);
        
        $this->assertEquals($newValues['id'], $basket->getId());
        $this->assertEquals($newValues['user_id'], $basket->getUserId());
        $this->assertEquals($newValues['billing_address'], $basket->getBillingAddress());
        $this->assertEquals($newValues['shipping_address'], $basket->getShippingAddress());
        $this->assertEquals($newValues['carrier'], $basket->getCarrier());
        $this->assertEquals($newValues['payment'], $basket->getPayment());
        $this->assertEquals($newValues['comment'], $basket->getComment());
        $this->assertEquals($newValues['shipping_cost'], $basket->getShippingCost());
        $this->assertEquals($newValues['total_amount'], $basket->getTotalAmount());
    }
    
    public function testImportArraySetsAllAttributes()
    {
        $newValues = [
            'id' => 2,
            'user_id' => 3,
            'billing_address' => 225,
            'shipping_address' => 285,
            'carrier' => Carrier::CARRIER_PICKUP,
            'payment' => Order::PAYMENT_CASH,
            'comment' => 'Towary potrzebne na wczoraj',
            'shipping_cost' => 15.00,
            'total_amount' => 65.65
        ];
        $basket = new Order($this->carriersGateway, $this->catalogProductsGateway);
        $basket->importArray($newValues);
        $this->assertEquals($newValues['id'], $basket->getId());
        $this->assertEquals($newValues['user_id'], $basket->getUserId());
        $this->assertEquals($newValues['billing_address'], $basket->getBillingAddress());
        $this->assertEquals($newValues['shipping_address'], $basket->getShippingAddress());
        $this->assertEquals($newValues['carrier'], $basket->getCarrier());
        $this->assertEquals($newValues['payment'], $basket->getPayment());
        $this->assertEquals($newValues['comment'], $basket->getComment());
        $this->assertEquals($newValues['shipping_cost'], $basket->getShippingCost());
        $this->assertEquals($newValues['total_amount'], $basket->getTotalAmount());
    }
    
    public function testValidateCallsInputValidatorAndReturnsObtainedValue()
    {
        $newValues = [
            'id' => 2,
            'user_id' => 3,
            'billing_address' => 225,
            'shipping_address' => 285,
            'carrier' => Carrier::CARRIER_PICKUP,
            'payment' => Order::PAYMENT_CASH,
            'comment' => 'Towary potrzebne na wczoraj',
            'shipping_cost' => 15.00,
            'total_amount' => 65.65
        ];
        $requiredFields = array_keys($newValues);
        
        $mockValidator1 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator1->method('validate')->willReturn(true);
        $result1 = Order::validate(
            $mockValidator1,
            $newValues,
            $requiredFields
        );
        $this->assertTrue($result1);
        
        $mockValidator2 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator2->method('validate')->willReturn(false);
        $result2 = Order::validate(
            $mockValidator2,
            $newValues,
            $requiredFields
        );
        $this->assertFalse($result2);
    }
    
    public function testChangingCarrierRecalculatesShippingCost()
    {
        $basket = new Order($this->carriersGateway, $this->catalogProductsGateway);
        $status = new OrderStatus();
        $status->setStatusId(OrderStatus::STATUS_BASKET);
        $basket->addStatus($status);
        $this->assertEquals(0.00, $basket->getShippingCost());
        $basket->setCarrier(2);
        $this->assertEquals(14.00, $basket->getShippingCost());
    }
}
