<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/OrderProduct.php';
require_once __DIR__ . '/../src/InputValidator.php';

class OrderProductTest extends PHPUnit_Framework_TestCase
{
    public function testConstructSetsDefaultEmptyValues()
    {
        $orderProduct = new OrderProduct();
        $this->assertEquals(-1, $orderProduct->getId());
        $this->assertEquals(-1, $orderProduct->getOrderId());
        $this->assertEquals(-1, $orderProduct->getProductId());
        $this->assertEquals(0, $orderProduct->getQuantity());
        $this->assertEquals(0.00, $orderProduct->getPrice());
    }
    
    public function testSettersSetRightValues()
    {
        $values = [
            'id' => 4,
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 1,
            'price' => 18.20,
        ];
        $orderProduct = new OrderProduct();
        $orderProduct->setId($values['id']);
        $orderProduct->setOrderId($values['order_id']);
        $orderProduct->setProductId($values['product_id']);
        $orderProduct->setQuantity($values['quantity']);
        $orderProduct->setPrice($values['price']);
        
        $this->assertEquals($values['id'], $orderProduct->getId());
        $this->assertEquals($values['order_id'], $orderProduct->getOrderId());
        $this->assertEquals($values['product_id'], $orderProduct->getProductId());
        $this->assertEquals($values['quantity'], $orderProduct->getQuantity());
        $this->assertEquals($values['price'], $orderProduct->getPrice());
    }

    public function testImportArraySetsAllAttributes()
    {
        $values = [
            'id' => 4,
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 1,
            'price' => 18.20,
        ];
        $orderProduct = new OrderProduct();
        $orderProduct->importArray($values);
        $this->assertEquals($values['id'], $orderProduct->getId());
        $this->assertEquals($values['order_id'], $orderProduct->getOrderId());
        $this->assertEquals($values['product_id'], $orderProduct->getProductId());
        $this->assertEquals($values['quantity'], $orderProduct->getQuantity());
        $this->assertEquals($values['price'], $orderProduct->getPrice());
    }
    
    public function testProductIdEqualsReturnsCorrectBoolean()
    {
        $values = [
            'id' => 4,
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 1,
            'price' => 18.20,
        ];
        $orderProduct = new OrderProduct();
        $orderProduct->importArray($values);
        $this->assertTrue($orderProduct->productIdEquals($values['product_id']));
        $this->assertFalse($orderProduct->productIdEquals($values['id']));
    }
    
    public function testValidateCallsInputValidatorAndReturnsObtainedValue()
    {
        $newValues = [
            'id' => 4,
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 1,
            'price' => 18.20,
        ];
        $requiredFields = array_keys($newValues);
        
        $mockValidator1 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator1->method('validate')->willReturn(true);
        $result1 = OrderProduct::validate(
            $mockValidator1,
            $newValues,
            $requiredFields
        );
        $this->assertTrue($result1);
        
        $mockValidator2 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator2->method('validate')->willReturn(false);
        $result2 = OrderProduct::validate(
            $mockValidator2,
            $newValues,
            $requiredFields
        );
        $this->assertFalse($result2);
    }
}
