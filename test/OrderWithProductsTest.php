<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Order.php';
require_once __DIR__ . '/../src/Product.php';
require_once __DIR__ . '/../src/Carrier.php';
require_once __DIR__ . '/../src/CarriersGateway.php';

class OrderWithProductsTest extends PHPUnit_Framework_TestCase
{
    private $products;
    private $basket;
    private $productPrice;
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
        
        $this->basket = new Order(new CarriersGateway($this->connection));

        $this->productPrice = 4.50;
        
        $productIds = [1, 2, 2]; //two products have the same id
        $this->products = [];
        foreach ($productIds as $productId) {
            $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
            $product->method('getId')->willReturn($productId);
            $product->method('getPrice')->willReturn($this->productPrice);
            $this->products[] = $product;
        }
    }
    
    protected function tearDown()
    {
        $this->products = null;
    }
    
    public function testAddingPossible()
    {
        $newProduct = $this->products[0];
        $this->basket->addProducts($newProduct, 2);
        $this->assertCount(1, $this->basket->getOrderProducts());
        $this->assertEquals(2, $this->basket->countProducts());
    }
    
    public function testAddingProductCreatesOrderProductFromProductWithCorrectReference()
    {
        $newProduct = $this->products[0];
        $this->basket->addProducts($newProduct, 2);
        $productFoundInTheBasket = $this->basket->getOrderProductById(
            $newProduct->getId()
        );
        $this->assertInstanceOf(OrderProduct::class, $productFoundInTheBasket);
        $this->assertEquals(
            $newProduct->getId(),
            $productFoundInTheBasket->getProductId()
        );
        $this->assertEquals(
            $newProduct->getPrice(),
            $productFoundInTheBasket->getPrice()
        );
    }
    
    public function testAddingDifferentProductExtendsList()
    {
        $this->basket->addProducts($this->products[0], 2);
        $this->basket->addProducts($this->products[1], 3);
        $this->assertCount(2, $this->basket->getOrderProducts());
        $this->assertEquals(5, $this->basket->countProducts());
        $this->assertEquals(
            $this->products[1]->getId(),
            $this->basket->getOrderProducts()[1]->getProductId()
        );
    }
    
    public function testAddingSameProductBasedOnIdOnlyIncreasesQuantity()
    {
        $this->basket->addProducts($this->products[1], 2);
        $this->basket->addProducts($this->products[1], 3);
        $this->assertCount(1, $this->basket->getOrderProducts());
        $this->assertEquals(5, $this->basket->countProducts());
        $this->basket->addProducts($this->products[2], 1); //this object has the same id as products[1]
        $this->assertCount(1, $this->basket->getOrderProducts());
        $this->assertEquals(6, $this->basket->countProducts());
    }
    
    public function testRemovingPossible()
    {
        $this->basket->addProducts($this->products[0], 3);
        $this->basket->removeProducts($this->products[0], 1);
        $this->assertCount(1, $this->basket->getOrderProducts());
        $this->assertEquals(2, $this->basket->countProducts());
    }
    
    public function testCannotRemoveProductNotPresentOnTheList()
    {
        $this->basket->addProducts($this->products[0], 3);
        $this->basket->removeProducts($this->products[1], 1);
        $this->assertCount(1, $this->basket->getOrderProducts());
        $this->assertEquals(3, $this->basket->countProducts());
    }
    
    public function testReducingQtyToZeroOrLessDoesNotDeleteItem()
    {
        $this->basket->addProducts($this->products[0], 3);
        $this->basket->removeProducts($this->products[0], 3);
        $this->assertCount(1, $this->basket->getOrderProducts());
        $this->assertEquals(0, $this->basket->countProducts());
    }

    public function testClearProductsReducedAllQtiesToZeroButDoesNotDeletesItems()
    {
        $this->basket->addProducts($this->products[0], 2);
        $this->basket->addProducts($this->products[1], 3);
        $this->basket->clearProducts();
        $this->assertCount(2, $this->basket->getOrderProducts());
        $this->assertEquals(0, $this->basket->countProducts());
    }
    
    public function testAddRemoveAndClearProductsRecalculatesTotalAmount()
    {
        $this->basket->setCarrier(Carrier::CARRIER_PICKUP); // no shipping cost
        $this->assertEquals(0, $this->basket->getTotalAmount());
        
        $this->basket->addProducts($this->products[0], 2);
        $this->assertEquals(2 * $this->productPrice, $this->basket->getTotalAmount());
        
        $this->basket->removeProducts($this->products[0], 1);
        $this->assertEquals(1 * $this->productPrice, $this->basket->getTotalAmount());
        
        $this->basket->clearProducts();
        $this->assertEquals(0, $this->basket->getTotalAmount());
    }
    
    public function testTotalAmountIncludesShippingCost()
    {
        $totalAmountWithNoProductsAndPickup = $this->basket->getTotalAmount();
        $this->assertEquals(0, $totalAmountWithNoProductsAndPickup);
        
        $this->basket->setCarrier(Carrier::CARRIER_DHL);
        $dhlCarrierCost = $this->basket->getShippingCost();
        $totalAmountWithNoProductsAndDHL = $this->basket->getTotalAmount();
        $this->assertEquals($dhlCarrierCost, $totalAmountWithNoProductsAndDHL);
        
        $this->basket->addProducts($this->products[0], 1);
        $productCost = $this->products[0]->getPrice();
        $expectedTotalAmount = $productCost + $dhlCarrierCost;
        $actualTotalAmountWithProductsAndDHL = $this->basket->getTotalAmount();
        $this->assertEquals($expectedTotalAmount, $actualTotalAmountWithProductsAndDHL);
    }
}
