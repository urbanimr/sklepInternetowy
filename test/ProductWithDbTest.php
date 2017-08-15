<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Product.php';

class ProductWithDbTest extends PHPUnit_Extensions_Database_TestCase
{
    private $connection;
    
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
    }
    
    public function testShowProductByIdReturnsProduct()
    {
        $product = Product::showProductById($this->connection, 1);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(1, $product->getId());
        $this->assertEquals('Mydło', $product->getName());
        $this->assertEquals(2.40, $product->getPrice());
        $this->assertEquals('Znakomity środek czystości dla kobiet i mężczyzn', $product->getDescription());
        $this->assertEquals(2, $product->getQuantity());
    }
    
    public function testShowAllProductsNameReturnsArrayOfNames()
    {
        $productNames = Product::showAllProductsName($this->connection);
        $this->assertInternalType('array', $productNames);
        $this->assertCount(3, $productNames);
        $this->assertEquals('Mydło', $productNames[0]);
        $this->assertEquals('Szydło', $productNames[1]);
    }
    
    public function testUploadProductToDataBaseUpdatesDbAndSetsId()
    {
        $product = new Product();
        
        $newValues = [
            'name' => 'Nowy produkt',
            'price' => 30.50,
            'description' => 'Opis super nowego produktu',
            'quantity' => 12
        ];
        $product->setName($newValues['name']);
        $product->setPrice($newValues['price']);
        $product->setDescription($newValues['description']);
        $product->setQuantity($newValues['quantity']);
        
        $originalId = $product->getId();
        $rowCountBeforeSaving = $this->getConnection()->getRowCount('products');
        $product->uploadProductToDataBase($this->connection);
        $rowCountAfterSaving = $this->getConnection()->getRowCount('products');
        $rowCountDifference = $rowCountAfterSaving - $rowCountBeforeSaving;
        $this->assertEquals(1, $rowCountDifference);
        $this->assertNotEquals($originalId, $product->getId());
    }
}