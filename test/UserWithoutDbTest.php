<?php
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/Address.php';
require_once __DIR__ . '/../src/InputValidator.php';

class UserWithoutDbTest extends PHPUnit_Framework_TestCase
{
    private $mockAddress;
    
    protected function setUp()
    {
        $this->mockAddress = $this->createMock(Address::class);
    }
    
    public function testConstructSetsDefaultEmptyValues()
    {
        $timeBeforeCreation = time();
        $user = new User();
        $timeAfterCreation = time();
        $this->assertEquals(-1, $user->getId());
        $this->assertEquals('', $user->getName());
        $this->assertEquals('', $user->getEmail());
        $this->assertEquals('', $user->getHashPass());
        $this->assertInternalType('integer', $user->getDateCreated());
        $this->assertGreaterThanOrEqual($timeBeforeCreation, $user->getDateCreated());
        $this->assertLessThanOrEqual($timeAfterCreation, $user->getDateCreated());
        $this->assertInternalType('array', $user->getAddresses());
        $this->assertEmpty($user->getAddresses());
    }
    
    public function testSettersSetRightValues()
    {
        $newValues = [
            'id' => '2',
            'name' => 'Jan Kowalski',
            'email' => 'jan@kowalski.pl',
            'dateCreated' => '2017-06-15 08:30:45',
        ];
        $user = new User();
        $user->setId($newValues['id']);
        $user->setName($newValues['name']);
        $user->setEmail($newValues['email']);
        $user->setDateCreated($newValues['dateCreated']);
        $this->assertEquals($newValues['id'], $user->getId());
        $this->assertEquals($newValues['name'], $user->getName());
        $this->assertEquals($newValues['email'], $user->getEmail());
        $this->assertEquals($newValues['dateCreated'], $user->getDateCreated());
    }
    
    public function testSetPasswordAndAuthenticateHashPassword()
    {
        $email = 'correct@email.com';
        $plainTextPassword = 'correctPASSWORD123';
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($plainTextPassword);
        $this->assertNotEquals($plainTextPassword, $user->getHashPass());
        $result1 = $user->authenticate($email, $plainTextPassword);
        $this->assertTrue($result1);
        $result2 = $user->authenticate($email, $user->getHashPass());
        $this->assertFalse($result2);
    }
    
    public function invalidAuthenticateData()
    {
        return [
            'invalidEmailValue' => ['incorrect@email.com', 'correctPASSWORD123'],
            'invalidPassValue' => ['correct@email.com', 'incorrectPASSWORD123']
        ];
    }
    
    /**
     * @dataProvider invalidAuthenticateData
     */
    public function testAuthenticateWithInvalidData($email, $password)
    {
        $correctEmail = 'correct@email.com';
        $correctPassword = 'correctPASSWORD123';
        $user = new User();
        $user->setEmail($correctEmail);
        $user->setPassword($correctPassword);
        $this->assertFalse($user->authenticate($email, $password));
    }
    
    public function testExchangeArraySetsAllAttributesAndHashedPassOverwritesLiteralOne()
    {
        $data1 = [
            'id' => '2',
            'name' => 'Jan Kowalski',
            'email' => 'jan@kowalski.pl',
            'password_plaintext' => 'correctPASSWORD123',
            'date_created' => '2017-06-15 08:30:45',
        ];
        $user1 = new User();
        $user1->exchangeArray($data1);
        $this->assertEquals($data1['id'], $user1->getId());
        $this->assertEquals($data1['name'], $user1->getName());
        $this->assertEquals($data1['email'], $user1->getEmail());
        $this->assertEquals($data1['date_created'], $user1->getDateCreated());
        $this->assertTrue($user1->authenticate($data1['email'], $data1['password_plaintext']));
        
        $data2 = [
            'id' => '2',
            'name' => 'Jan Kowalski',
            'email' => 'jan@kowalski.pl',
            'password' => '$2y$11$KEf2QAk/Mpw2nR8OUKc0N.pZlC/d.zoZoX.lCqKph/Gy9Ejz6aUKu',
            'password_plaintext' => 'correctPASSWORD123',
            'date_created' => '2017-06-15 08:30:45',
        ];
        $user2 = new User();
        $user2->exchangeArray($data2);
        $this->assertFalse($user2->authenticate($data2['email'], $data2['password_plaintext']));
    }
    
    public function testValidateCallsInputValidatorAndReturnsObtainedValue()
    {
        $data = [
            'id' => 1,
            'name' => 'Jan Kowalski',
            'email' => 'jan@kowalski.pl',
            'password' => 'abcABC123',
            'dateCreated' => '2017-06-15 08:30:45'
        ];
        $requiredFields = array_keys($data);
        
        $mockValidator1 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator1->method('validate')->willReturn(true);
        $result1 = User::validate(
            $mockValidator1,
            $data,
            $requiredFields
        );
        $this->assertTrue($result1);
        
        $mockValidator2 = $this->getMockBuilder(InputValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockValidator2->method('validate')->willReturn(false);
        $result2 = User::validate(
            $mockValidator2,
            $data,
            $requiredFields
        );
        $this->assertFalse($result2);
    }

    
}