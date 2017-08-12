<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Address.php';
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/InputValidator.php';

class AddressWithDbTest extends PHPUnit_Extensions_Database_TestCase
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
    
    public function testLoadAddressByColumn()
    {
        $values = [
            'id' => 1,
            'alias' => 'Biuro',
            'company' => 'Przetwory grzybne Sromotnik spółka z o.o. sp. k.',
            'name' => 'John Doe',
            'address1' => 'ul. Leśna 12',
            'address2' => 'lokal 12',
            'postcode' => '84-110',
            'city' => 'Kartoszyno',
            'country' => 'Polska',
            'phone' => '225678743',
            'tax_no' => '5674325676'
        ];
        
        $address = Address::loadAddressByColumn($this->connection, 'id', 1);
        
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($values['id'], $address->getId());
        $this->assertEquals($values['alias'], $address->getAlias());
        $this->assertEquals($values['company'], $address->getCompany());
        $this->assertEquals($values['name'], $address->getName());
        $this->assertEquals($values['address1'], $address->getAddress1());
        $this->assertEquals($values['address2'], $address->getAddress2());
        $this->assertEquals($values['postcode'], $address->getPostcode());
        $this->assertEquals($values['city'], $address->getCity());
        $this->assertEquals($values['country'], $address->getCountry());
        $this->assertEquals($values['phone'], $address->getPhone());
        $this->assertEquals($values['tax_no'], $address->getTaxNo());
    }
    
    public function testSaveWithNonPersistedAddressInsertsRowChangesIdReturnsTrue()
    {
        $newAddress = new Address();
        $values = [
            'alias' => 'Biuro',
            'company' => 'Przetwory grzybne Sromotnik spółka z o.o. sp. k.',
            'name' => 'John Doe',
            'address1' => 'ul. Leśna 12',
            'address2' => 'lokal 12',
            'postcode' => '84-110',
            'city' => 'Kartoszyno',
            'country' => 'Polska',
            'phone' => '225678743',
            'tax_no' => '5674325676'
        ];
        $newAddress->exchangeArray($values);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('addresses');
        $result = $newAddress->save($this->connection);
        $this->assertTrue($result);
        $this->assertNotEquals(-1, $newAddress->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('addresses');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testSaveWithPersistedAddressOnlyUpdatesRowAndReturnsTrue()
    {
        $addressId = 1;
        $address = Address::loadAddressByColumn($this->connection, 'id', $addressId);
        $rowCountBeforeUpdate = $this->getConnection()->getRowCount('addresses');
        $newValues = [
            'alias' => 'Biuro nr 1',
            'company' => 'Rozlewnia soków SA',
            'name' => 'Maria Nowak',
            'address1' => 'ul. Miejska 12',
            'address2' => 'lokal 45',
            'postcode' => '12-433',
            'city' => 'Ciemnogród',
            'country' => 'Polska',
            'phone' => '33 765 76 65',
            'tax_no' => '546-987-58-55'
        ];
        $address->exchangeArray($newValues);
        $result = $address->save($this->connection);
        $this->assertTrue($result);
        $this->assertEquals($addressId, $address->getId());
        $rowCountAfterUpdate = $this->getConnection()->getRowCount('addresses');
        $this->assertEquals($rowCountBeforeUpdate, $rowCountAfterUpdate);
        $address = null;
        $newAddress = Address::loadAddressByColumn($this->connection, 'id', $addressId);
        $this->assertEquals($newValues['alias'], $newAddress->getAlias());
        $this->assertEquals($newValues['company'], $newAddress->getCompany());
        $this->assertEquals($newValues['name'], $newAddress->getName());
        $this->assertEquals($newValues['address1'], $newAddress->getAddress1());
        $this->assertEquals($newValues['address2'], $newAddress->getAddress2());
        $this->assertEquals($newValues['postcode'], $newAddress->getPostcode());
        $this->assertEquals($newValues['city'], $newAddress->getCity());
        $this->assertEquals($newValues['country'], $newAddress->getCountry());
        $this->assertEquals($newValues['phone'], $newAddress->getPhone());
        $this->assertEquals($newValues['tax_no'], $newAddress->getTaxNo());
    }
}
