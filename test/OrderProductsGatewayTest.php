<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/OrderProduct.php';
require_once __DIR__ . '/../src/OrderProductsGateway.php';
require_once __DIR__ . '/../src/InputValidator.php';
require_once __DIR__ . '/../src/Product.php';

class OrderProductsGatewayTest extends PHPUnit_Extensions_Database_TestCase
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
        
        $this->gateway = new OrderProductsGateway($this->connection);
    }
    
    public function testLoadProductsByOrderLoadsOrderProductObjects()
    {
        $orderProducts = $this->gateway->loadProductsByOrderId(1);
        $this->assertInternalType('array', $orderProducts);
        $this->assertInstanceOf(OrderProduct::class, $orderProducts[0]);
        $this->assertNotEquals($orderProducts[0]->getId(), $orderProducts[1]->getId());
    }
    
    public function testLoadProductsWithDefaultParamsLoadsItemsSortedByIdAsc()
    {
        $orderProducts = $this->gateway->loadProductsByOrderId(1);
        $ids = [];
        foreach ($orderProducts as $product) {
            $ids[] = $product->getId();
        }
        $idsInExpectedOrder = $ids;
        sort($idsInExpectedOrder);
        $this->assertSame($idsInExpectedOrder, $ids);
    }
    
    public function testLoadedProductsContainReferencedCatalogProducts()
    {
        $orderProducts = $this->gateway->loadProductsByOrderId(1);
        foreach ($orderProducts as $orderProduct) {
            $this->assertInstanceOf(Product::class, $orderProduct->getCatalogProduct());
            $this->assertEquals(
                $orderProduct->getProductId(),
                $orderProduct->getCatalogProduct()->getId()
            );
        }
    }
    
    public function testSaveWithNonPersistedProductInsertsRowChangesIdReturnsTrue()
    {
        $values = [
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 1,
            'price' => 18.20,
        ];
        $orderProduct = new OrderProduct();
        $orderProduct->importArray($values);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('order_products');
        $result = $this->gateway->save($orderProduct);
        $this->assertTrue($result);
        $this->assertNotEquals(-1, $orderProduct->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('order_products');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testSaveWithPersistedProductOnlyUpdatesRowAndReturnsTrue()
    {
        $orderId = 1;
        $orderProducts = $this->gateway->loadProductsByOrderId($orderId);
        $orderProduct = $orderProducts[0];
        $originalId = $orderProduct->getId();
        $newValues = [
            'quantity' => 6,
            'price' => 18.20,
        ];
        $orderProduct->importArray($newValues);
        $rowCountBeforeUpdate = $this->getConnection()->getRowCount('order_products');
        $result = $this->gateway->save($orderProduct);
        $this->assertTrue($result);
        $this->assertEquals($originalId, $orderProduct->getId());
        $rowCountAfterUpdate = $this->getConnection()->getRowCount('order_products');
        $rowCountDifference = $rowCountAfterUpdate - $rowCountBeforeUpdate;
        $this->assertEquals(0, $rowCountDifference);
    }
    
    public function testSaveWithPersistedProductAndZeroQuantityCallsDelete()
    {
        $mockGateway = $this->getMockBuilder(OrderProductsGateway::class)
            ->setConstructorArgs([$this->connection])
            ->setMethods(['delete'])
            ->getMock();
        $mockGateway->method('delete')->willReturn('OK');
        
        $orderId = 1;
        $orderProducts = $this->gateway->loadProductsByOrderId($orderId);
        $orderProduct = $orderProducts[0];
        $orderProduct->setQuantity(0);
        $result = $mockGateway->save($orderProduct);
        $this->assertEquals('OK', $result);
    }
    
    public function testSaveWithNonPersistedProductAndZeroQuantityDoesNotInsert()
    {
        $values = [
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 0,
            'price' => 18.20,
        ];
        $orderProduct = new OrderProduct();
        $orderProduct->importArray($values);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('order_products');
        $result = $this->gateway->save($orderProduct);
        $this->assertFalse($result);
        $this->assertEquals(-1, $orderProduct->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('order_products');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(0, $rowCountDifference);
    }

    public function testDeleteWithPersistedProductRemovesRowReturnsTrueAndChangesId()
    {
        $orderId = 1;
        $orderProducts = $this->gateway->loadProductsByOrderId($orderId);
        $orderProduct = $orderProducts[0];
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('order_products');
        $result = $this->gateway->delete($orderProduct);
        $this->assertTrue($result);
        $this->assertEquals(-1, $orderProduct->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('order_products');
        $rowCountDifference = $rowCountBeforeDelete - $rowCountAfterDelete;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testDeleteWithNonPersistedProductOnlyReturnsFalse()
    {
        $values = [
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 1,
            'price' => 18.20,
        ];
        $orderProduct = new OrderProduct();
        $orderProduct->importArray($values);
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('order_products');
        $result = $this->gateway->delete($orderProduct);
        $this->assertFalse($result);
        $this->assertEquals(-1, $orderProduct->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('order_products');
        $rowCountDifference = $rowCountAfterDelete - $rowCountBeforeDelete;
        $this->assertEquals(0, $rowCountDifference);
    }
}
