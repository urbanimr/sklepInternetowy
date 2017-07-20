<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/OrderStatus.php';
require_once __DIR__ . '/../src/OrderStatusesGateway.php';
require_once __DIR__ . '/../src/InputValidator.php';

class OrderStatusesGatewayTest extends PHPUnit_Extensions_Database_TestCase
{
    private $connection;
    private $gateway;
    
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
        
        $this->gateway = new OrderStatusesGateway($this->connection);
    }
    
    public function testLoadStatusesByOrderLoadsOrderStatusObjects()
    {
        $statuses = $this->gateway->loadStatusesByOrderId(1);
        $this->assertInternalType('array', $statuses);
        $this->assertInstanceOf(OrderStatus::class, $statuses[0]);
        $this->assertNotEquals($statuses[0]->getId(), $statuses[1]->getId());
    }
    
    public function testLoadStatusesWithDefaultParamsLoadsItemsSortedByDateAsc()
    {
        $statuses = $this->gateway->loadStatusesByOrderId(1);
        $dateValues = [];
        foreach ($statuses as $status) {
            $dateValues[] = strtotime($status->getDate());
        }
        $dateValuesInExpectedOrder = $dateValues;
        sort($dateValuesInExpectedOrder);
        $this->assertSame($dateValuesInExpectedOrder, $dateValues);
    }
    
    public function testSaveInsertsRowReturnsTrueAndChangesId()
    {
        $values = [
            'status_id' => OrderStatus::STATUS_SUBMITTED,
            'status_name' => 'Submitted',
            'order_id' => 1,
            'date' => '2017-07-22 16:20:15'
        ];
        $status = new OrderStatus();
        $status->importArray($values);
        
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('order_statuses');
        
        $result = $this->gateway->save($status);
        
        
        $this->assertTrue($result);
        $this->assertNotEquals(-1, $status->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('order_statuses');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testSaveWithAlreadyPersistedStatusNotPossible()
    {
        $orderId = 1;
        $statusesBeforeInsert = $this->gateway->loadStatusesByOrderId($orderId);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('order_statuses');
        $singleStatus = $statusesBeforeInsert[0];
        $singleStatus->setDate('2017-07-22 16:20:15');
        
        $result = $this->gateway->save($singleStatus);
        
        $this->assertFalse($result);
        $rowCountAfterInsert = $this->getConnection()->getRowCount('order_statuses');
        $this->assertEquals($rowCountBeforeInsert, $rowCountAfterInsert);
    }
    
    public function testDeleteWithPersistedStatusRemovesRowReturnsTrueChangesId()
    {
        $orderId = 1;
        $statusesBeforeDelete = $this->gateway->loadStatusesByOrderId($orderId);
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('order_statuses');
        $singleStatus = $statusesBeforeDelete[0];
        
        $result = $this->gateway->delete($singleStatus);
        
        $this->assertTrue($result);
        $this->assertEquals(-1, $singleStatus->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('order_statuses');
        $rowCountDifference = $rowCountBeforeDelete - $rowCountAfterDelete;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testDeleteWithNonPersistedStatusReturnsFalseAndDoesNotRemoveRow()
    {
        $values = [
            'status_id' => OrderStatus::STATUS_SUBMITTED,
            'status_name' => 'Submitted',
            'order_id' => 1,
            'date' => '2017-07-22 16:20:15'
        ];
        $status = new OrderStatus();
        $status->importArray($values);
        
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('order_statuses');
        
        $result = $this->gateway->delete($status);
        
        $this->assertFalse($result);
        $this->assertEquals(-1, $status->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('order_statuses');
        $rowCountDifference = $rowCountBeforeDelete - $rowCountAfterDelete;
        $this->assertEquals(0, $rowCountDifference);
    }
}