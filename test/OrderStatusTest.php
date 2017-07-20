<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/OrderStatus.php';
require_once __DIR__ . '/../src/InputValidator.php';

class OrderStatusTest extends PHPUnit_Framework_TestCase
{
    public function testConstructSetsDefaultEmptyValues()
    {
        $status = new OrderStatus();
        $this->assertEquals(-1, $status->getId());
        $this->assertEquals(-1, $status->getStatusId());
        $this->assertEquals('', $status->getStatusName());
        $this->assertEquals(-1, $status->getOrderId());
        $this->assertEquals('0000-00-00 00:00:00', $status->getDate());
    }
    
    public function testSettersSetRightValues()
    {
        $values = [
            'id' => 4,
            'status_id' => OrderStatus::STATUS_SUBMITTED,
            'status_name' => 'Submitted',
            'order_id' => 1,
            'date' => '2017-07-01 12:20:15'
        ];
        $status = new OrderStatus();
        $status->setId($values['id']);
        $status->setStatusId($values['status_id']);
        $status->setStatusName($values['status_name']);
        $status->setOrderId($values['order_id']);
        $status->setDate($values['date']);
        
        $this->assertEquals($values['id'], $status->getId());
        $this->assertEquals($values['status_id'], $status->getStatusId());
        $this->assertEquals($values['status_name'], $status->getStatusName());
        $this->assertEquals($values['order_id'], $status->getOrderId());
        $this->assertEquals($values['date'], $status->getDate());
    }

    public function testImportArraySetsAllAttributes()
    {
        $values = [
            'id' => 4,
            'status_id' => OrderStatus::STATUS_SUBMITTED,
            'status_name' => 'Submitted',
            'order_id' => 1,
            'date' => '2017-07-01 12:20:15'
        ];
        $status = new OrderStatus();
        $status->importArray($values);
        $this->assertEquals($values['id'], $status->getId());
        $this->assertEquals($values['status_id'], $status->getStatusId());
        $this->assertEquals($values['status_name'], $status->getStatusName());
        $this->assertEquals($values['order_id'], $status->getOrderId());
        $this->assertEquals($values['date'], $status->getDate());
    }
    
    public function testCreateNewStatusReturnsNewStatusWithCorrectValues()
    {
        $values = [
            'id' => 4,
            'status_id' => OrderStatus::STATUS_SUBMITTED,
            'status_name' => 'Submitted',
            'order_id' => 1,
            'date' => '2017-07-01 12:20:15'
        ];
        $status = new OrderStatus();
        $status->importArray($values);
        
        $timeBeforeCreation = time();
        $newStatus = $status->createNewStatus(OrderStatus::STATUS_CANCELED);
        $timeAfterCreation = time();
        
        $this->assertNotSame($newStatus, $status);
        $this->assertEquals(-1, $newStatus->getId());
        $this->assertEquals(OrderStatus::STATUS_CANCELED, $newStatus->getStatusId());
        $this->assertEquals('', $newStatus->getStatusName());
        $this->assertEquals($values['order_id'], $newStatus->getOrderId());
        $this->assertGreaterThanOrEqual(
            date('Y-m-d H:i:s', $timeBeforeCreation),
            $newStatus->getDate()
        );
        $this->assertLessThanOrEqual(
            date('Y-m-d H:i:s', $timeAfterCreation),
            $newStatus->getDate()
        );
    }
    
    public function testValidateCallsInputValidatorAndReturnsObtainedValue()
    {
        $values = [
            'id' => 4,
            'status_id' => OrderStatus::STATUS_SUBMITTED,
            'status_name' => 'Submitted',
            'order_id' => 1,
            'date' => '2017-07-01 12:20:15'
        ];
        $requiredFields = array_keys($values);
        
        $mockValidator1 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator1->method('validate')->willReturn(true);
        $result1 = OrderStatus::validate(
            $mockValidator1,
            $values,
            $requiredFields
        );
        $this->assertTrue($result1);
        
        $mockValidator2 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator2->method('validate')->willReturn(false);
        $result2 = OrderStatus::validate(
            $mockValidator2,
            $values,
            $requiredFields
        );
        $this->assertFalse($result2);
    }
}
