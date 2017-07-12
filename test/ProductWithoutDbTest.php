<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Product.php';

class ProductWithoutDbTest extends PHPUnit_Framework_TestCase
{
    public function testConstructSetsDefaultEmptyValues()
    {
        $product = new Product();
        $this->assertEquals(-1, $product->getId());
        $this->assertEquals('', $product->getName());
        $this->assertEquals(0, $product->getPrice());
        $this->assertEquals('', $product->getDescription());
        $this->assertEquals(0, $product->getQuantity());
    }
    
    public function testSettersSetRightValues()
    {
        $newValues = [
            'id' => '4',
            'name' => 'Nowy produkt',
            'price' => 30.50,
            'description' => 'Opis super nowego produktu',
            'quantity' => 12
        ];
        $product = new Product();
        $product->setId($newValues['id']);
        $product->setName($newValues['name']);
        $product->setPrice($newValues['price']);
        $product->setDescription($newValues['description']);
        $product->setQuantity($newValues['quantity']);        
        $this->assertEquals($newValues['id'], $product->getId());
        $this->assertEquals($newValues['name'], $product->getName());
        $this->assertEquals($newValues['price'], $product->getPrice());
        $this->assertEquals($newValues['description'], $product->getDescription());
        $this->assertEquals($newValues['quantity'], $product->getQuantity());
    }
    
    public function testBuyProductChangeQuantity()
    {
        $product = new Product();
        $originalQuantity = 0;
        $product->setQuantity($originalQuantity);
        
        $product->buyProduct(3);
        $quantityAfterPurchase = $product->getQuantity();
        $difference1 = $quantityAfterPurchase - $originalQuantity;
        $this->assertEquals(3, $difference1);
        
        $product->sellProduct(1);
        $quantityAfterFirstSale = $product->getQuantity();
        $difference2 = $quantityAfterPurchase - $quantityAfterFirstSale;
        $this->assertEquals(1, $difference2);
        
        $product->sellProduct(2);
        $quantityAfterSecondSale = $product->getQuantity();
        $this->assertEquals($originalQuantity, $quantityAfterSecondSale);
    }

    public function testSellProductNotPossibleWhenNotEnoughStock()
    {
        $product = new Product();
        $originalQuantity = 1;
        $product->setQuantity($originalQuantity);
        $product->sellProduct(2);
        $this->assertEquals($originalQuantity, $product->getQuantity());
    }
}