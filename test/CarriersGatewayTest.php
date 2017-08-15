<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/CarriersGateway.php';

class CarriersGatewayTest extends PHPUnit_Extensions_Database_TestCase
{
    private $connection;
    private $gateway;
    
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
        
        $this->gateway = new CarriersGateway($this->connection);
    }
    
    public function testLoadAllCarriers()
    {
        $carriers = $this->gateway->loadAllCarriers();
        $this->assertInternalType('array', $carriers);
        $this->assertInstanceOf(Carrier::class, $carriers[0]);
        $this->assertNotEquals($carriers[0]->getId(), $carriers[1]->getId());
        $activeCarriersCounter = 0;
        $disabledCarriersCounter = 0;
        foreach ($carriers as $carrier) {
            if ($carrier->getActive()) {
                $activeCarriersCounter++;
            } else {
                $disabledCarriersCounter++;
            }
        }
        $this->assertGreaterThan(0, $activeCarriersCounter);
        $this->assertGreaterThan(0, $disabledCarriersCounter);
    }
    
    public function testLoadActiveCarriers()
    {
        $carriers = $this->gateway->loadActiveCarriers();
        $this->assertInternalType('array', $carriers);
        $this->assertInstanceOf(Carrier::class, $carriers[0]);
        $this->assertNotEquals($carriers[0]->getId(), $carriers[1]->getId());
        foreach ($carriers as $carrier) {
            $this->assertTrue($carrier->getActive());
        }
    }
    
    public function testLoadCarrierById()
    {
        $carrierOneData = [
            'id'    => 1,
            'carrier_name'  => 'In-store pickup',
            'description' => 'Visit our store and pick up your order free of charge',
            'price' => 0.00,
            'active' => 1
        ];

        $carrier = $this->gateway->loadCarrierById($carrierOneData['id']);
        $this->assertInstanceOf(Carrier::class, $carrier);
        $this->assertEquals($carrierOneData['carrier_name'], $carrier->getCarrierName());
        $this->assertEquals($carrierOneData['description'], $carrier->getDescription());
        $this->assertEquals($carrierOneData['price'], $carrier->getPrice());
        $this->assertEquals($carrierOneData['active'], $carrier->getActive());
    }
}
