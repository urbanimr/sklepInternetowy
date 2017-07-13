<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/Address.php';

class AddressDeleteTest extends PHPUnit_Extensions_Database_TestCase
{
    private $connection;
    private $newAddress;
    
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

        $addressValues = [
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
        $this->newAddress = new Address();
        $this->newAddress->exchangeArray($addressValues);
    }
    
    public function testDeleteAddressWithPersistedItemUpdatesDbChangesIdAndReturnsTrue()
    {
        $this->newAddress->save($this->connection);
        $addressId = $this->newAddress->getId();
        $this->newAddress = null;
        
        $loadedAddress = Address::loadAddressByColumn($this->connection, 'id', $addressId);
        $this->assertInstanceOf(Address::class, $loadedAddress);
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('addresses');
        $result = $loadedAddress->delete($this->connection);
        $this->assertTrue($result);
        $this->assertEquals(-1, $loadedAddress->getId());
        $this->assertNull(Address::loadAddressByColumn($this->connection, 'id', $addressId));
        $rowCountAfterDelete = $this->getConnection()->getRowCount('addresses');
        $rowCountDifference = $rowCountBeforeDelete - $rowCountAfterDelete;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testDeleteAddressWithNonPersistedItemOnlyReturnsFalse()
    {
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('addresses');
        $result = $this->newAddress->delete($this->connection);
        $this->assertFalse($result);
        $this->assertEquals(-1, $this->newAddress->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('addresses');
        $this->assertEquals($rowCountBeforeDelete, $rowCountAfterDelete);
    }
    
    public function testDeleteBillingAddressFails()
    {
        $this->newAddress->save($this->connection);
        $user = User::loadUserByColumn($this->connection, 'id', 1);
        $user->setBillingAddressId($this->newAddress->getId());
        $user->save($this->connection);
        
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('addresses');
        $result = $this->newAddress->delete($this->connection);
        $this->assertFalse($result);
        $this->assertNotEquals(-1, $this->newAddress->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('addresses');
        $this->assertEquals($rowCountBeforeDelete, $rowCountAfterDelete);
    }
    
    public function testDeleteShippingAddressPossible()
    {
        
        $this->newAddress->save($this->connection);
        $user = User::loadUserByColumn($this->connection, 'id', 1);
        $user->setShippingAddressId($this->newAddress->getId());
        $user->save($this->connection);
        
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('addresses');
        $result = $this->newAddress->delete($this->connection);
        $this->assertTrue($result);
        $this->assertEquals(-1, $this->newAddress->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('addresses');
        $rowCountDifference = $rowCountBeforeDelete - $rowCountAfterDelete;
        $this->assertEquals(1, $rowCountDifference);
    }
}
