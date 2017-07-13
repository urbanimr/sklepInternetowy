<?php
require_once __DIR__ . '/../src/Address.php';
require_once __DIR__ . '/../src/InputValidator.php';

class AddressWithoutDbTest extends PHPUnit_Framework_TestCase
{
    public function testConstructSetsDefaultEmptyValues()
    {
        $address = new Address();
        $this->assertEquals(-1, $address->getId());
        $this->assertEquals('', $address->getAlias());
        $this->assertEquals('', $address->getCompany());
        $this->assertEquals('', $address->getName());
        $this->assertEquals('', $address->getAddress1());
        $this->assertEquals('', $address->getAddress2());
        $this->assertEquals('', $address->getPostcode());
        $this->assertEquals('', $address->getCity());
        $this->assertEquals('', $address->getCountry());
        $this->assertEquals('', $address->getPhone());
        $this->assertEquals('', $address->getTaxNo());
    }
    
    public function testSettersSetRightValues()
    {
        $newValues = [
            'id' => 2,
            'alias' => 'Mój dom',
            'company' => 'Kowalski S.A.',
            'name' => 'Jan Kowalski',
            'address1' => 'ul. Długa 11/2',
            'address2' => '7. piętro',
            'postcode' => '16-543',
            'city' => 'Ciemnogród',
            'country' => 'Polska',
            'phone' => '228765432',
            'tax_no' => '4567654567'
        ];
        $address = new Address();
        $address->setId($newValues['id']);
        $address->setAlias($newValues['alias']);
        $address->setCompany($newValues['company']);
        $address->setName($newValues['name']);
        $address->setAddress1($newValues['address1']);
        $address->setAddress2($newValues['address2']);
        $address->setPostcode($newValues['postcode']);
        $address->setCity($newValues['city']);
        $address->setCountry($newValues['country']);
        $address->setPhone($newValues['phone']);
        $address->setTaxNo($newValues['tax_no']);
        
        $this->assertEquals($newValues['id'], $address->getId());
        $this->assertEquals($newValues['alias'], $address->getAlias());
        $this->assertEquals($newValues['company'], $address->getCompany());
        $this->assertEquals($newValues['name'], $address->getName());
        $this->assertEquals($newValues['address1'], $address->getAddress1());
        $this->assertEquals($newValues['address2'], $address->getAddress2());
        $this->assertEquals($newValues['postcode'], $address->getPostcode());
        $this->assertEquals($newValues['city'], $address->getCity());
        $this->assertEquals($newValues['country'], $address->getCountry());
        $this->assertEquals($newValues['phone'], $address->getPhone());
        $this->assertEquals($newValues['tax_no'], $address->getTaxNo());
    }

    public function testExchangeArraySetsAllAttributes()
    {
        $newValues = [
            'id' => 2,
            'alias' => 'Mój dom',
            'company' => 'Kowalski S.A.',
            'name' => 'Jan Kowalski',
            'address1' => 'ul. Długa 11/2',
            'address2' => '7. piętro',
            'postcode' => '16-543',
            'city' => 'Ciemnogród',
            'country' => 'Polska',
            'phone' => '228765432',
            'tax_no' => '4567654567'
        ];
        $address = new Address();
        $address->exchangeArray($newValues);
        $this->assertEquals($newValues['id'], $address->getId());
        $this->assertEquals($newValues['alias'], $address->getAlias());
        $this->assertEquals($newValues['company'], $address->getCompany());
        $this->assertEquals($newValues['name'], $address->getName());
        $this->assertEquals($newValues['address1'], $address->getAddress1());
        $this->assertEquals($newValues['address2'], $address->getAddress2());
        $this->assertEquals($newValues['postcode'], $address->getPostcode());
        $this->assertEquals($newValues['city'], $address->getCity());
        $this->assertEquals($newValues['country'], $address->getCountry());
        $this->assertEquals($newValues['phone'], $address->getPhone());
        $this->assertEquals($newValues['tax_no'], $address->getTaxNo());
    }
    
    public function testValidateCallsInputValidatorAndReturnsObtainedValue()
    {
        $newValues = [
            'id' => 2,
            'alias' => 'Mój dom',
            'company' => 'Kowalski S.A.',
            'name' => 'Jan Kowalski',
            'address1' => 'ul. Długa 11/2',
            'address2' => '7. piętro',
            'postcode' => '16-543',
            'city' => 'Ciemnogród',
            'country' => 'Polska',
            'phone' => '228765432',
            'tax_no' => '4567654567'
        ];
        $requiredFields = array_keys($newValues);
        
        $mockValidator1 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator1->method('validate')->willReturn(true);
        $result1 = Address::validate(
            $mockValidator1,
            $newValues,
            $requiredFields
        );
        $this->assertTrue($result1);
        
        $mockValidator2 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator2->method('validate')->willReturn(false);
        $result2 = Address::validate(
            $mockValidator2,
            $newValues,
            $requiredFields
        );
        $this->assertFalse($result2);
    }
}
