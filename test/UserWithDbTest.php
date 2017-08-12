<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/InputValidator.php';

class UserWithDbTest extends PHPUnit_Extensions_Database_TestCase
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
    
    public function testLoadUserByColumn()
    {
        $john = User::loadUserByColumn($this->connection, 'id', 1);
        $this->assertInstanceOf(User::class, $john);
        $this->assertEquals(1, $john->getId());
        $this->assertEquals('John Doe', $john->getName());
        $this->assertEquals('john@doe.com', $john->getEmail());
        $this->assertTrue($john->authenticate('john@doe.com', 'doe'));
        $this->assertEquals('2017-07-01 12:20:15', $john->getDateCreated());
        $this->assertEquals(1, $john->getBillingAddressId());
        $this->assertEquals(2, $john->getShippingAddressId());
    }
    
    public function testLoadManyUsersLoadsUserObjects()
    {
        $users = User::loadManyUsers($this->connection);
        $this->assertInternalType('array', $users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertNotEquals($users[0]->getId(), $users[1]->getId());
    }
    
    public function testLoadManyUsersWithDefaultParamsLoadsUsersSortedByDateDescending()
    {
        $users = User::loadManyUsers($this->connection);
        $dateValues = [];
        foreach ($users as $user) {
            $dateValues[] = strtotime($user->getDateCreated());
        }
        $dateValuesInExpectedOrder = $dateValues;
        rsort($dateValuesInExpectedOrder);
        $this->assertSame($dateValuesInExpectedOrder, $dateValues);
    }
    
    public function testLoadManyUsersReturnsCorrectSetOfUsers()
    {
        $largerGroupOfUsers = User::loadManyUsers(
            $this->connection,
            4,
            0,
            'date_created',
            false
        );
        $smallerGroupOfUsers = User::loadManyUsers(
            $this->connection,
            2,
            1,
            'date_created',
            false
        );
        $this->assertCount(4, $largerGroupOfUsers);
        $this->assertCount(2, $smallerGroupOfUsers);
        $this->assertEquals(
            $largerGroupOfUsers[1]->getId(),
            $smallerGroupOfUsers[0]->getId()
        );
        $this->assertEquals(
            $largerGroupOfUsers[2]->getId(),
            $smallerGroupOfUsers[1]->getId()
        );
    }
    
    public function testDeleteUserWithPersistedUserUpdatesDbChangesIdAndReturnsTrue()
    {
        $idOfJohn = 1;
        $john = User::loadUserByColumn($this->connection, 'id', $idOfJohn);
        $this->assertInstanceOf(User::class, $john);
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('users');
        $result = $john->delete($this->connection);
        $this->assertTrue($result);
        $this->assertEquals(-1, $john->getId());
        $this->assertNull(User::loadUserByColumn($this->connection, 'id', $idOfJohn));
        $rowCountAfterDelete = $this->getConnection()->getRowCount('users');
        $rowCountDifference = $rowCountBeforeDelete - $rowCountAfterDelete;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testDeleteUserWithNonPersistedUserOnlyReturnsFalse()
    {
        $newUser = new User();
        $newUser->exchangeArray([
            'name' => 'Jakub Stonoga',
            'email' => 'jakub@stonoga.pl',
            'password_plaintext' => 'correctPASSWORD123',
            'date_created' => '2017-07-01 12:20:15'
        ]);
        $rowCountBeforeDelete = $this->getConnection()->getRowCount('users');
        $result = $newUser->delete($this->connection);
        $this->assertFalse($result);
        $this->assertEquals(-1, $newUser->getId());
        $rowCountAfterDelete = $this->getConnection()->getRowCount('users');
        $this->assertEquals($rowCountBeforeDelete, $rowCountAfterDelete);
    }
    
    public function testSaveWithNonPersistedUserInsertsRowChangesIdReturnsTrue()
    {
        $newUser = new User();
        $newUser->exchangeArray([
            'name' => 'Jakub Stonoga',
            'email' => 'jakub@stonoga.pl',
            'password_plaintext' => 'correctPASSWORD123',
            'date_created' => '2017-07-01 12:20:15',
            'billing_address' => 1,
            'shipping_address' => 1
        ]);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('users');
        $result = $newUser->save($this->connection);
        $this->assertTrue($result);
        $this->assertNotEquals(-1, $newUser->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('users');
        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
        $this->assertEquals(1, $rowCountDifference);
    }
    
    public function testSaveWithPersistedUseOnlyUpdatesRowAndReturnsTrue()
    {
        $idOfJohn = 1;
        $john = User::loadUserByColumn($this->connection, 'id', $idOfJohn);
        $rowCountBeforeUpdate = $this->getConnection()->getRowCount('users');
        $newValues = [
            'name' => 'Jakub Stonoga',
            'email' => 'jakub@stonoga.pl',
            'password_plaintext' => 'correctPASSWORD123',
            'date_created' => '2017-07-01 12:20:15',
            'billing_address' => 1,
            'shipping_address' => 1
        ];
        $john->exchangeArray($newValues);
        $result = $john->save($this->connection);
        $this->assertTrue($result);
        $this->assertEquals($idOfJohn, $john->getId());
        $rowCountAfterUpdate = $this->getConnection()->getRowCount('users');
        $this->assertEquals($rowCountBeforeUpdate, $rowCountAfterUpdate);
        $john = null;
        $newJohn = User::loadUserByColumn($this->connection, 'id', $idOfJohn);
        $this->assertEquals($newValues['name'], $newJohn->getName());
    }
    
    public function testSavingUserWithTheSameEmailNotPossible()
    {
        $emailOfJohnDoe = 'john@doe.com';
        $newUser = new User();
        $newUser->exchangeArray([
            'name' => 'Jakub Stonoga',
            'email' => $emailOfJohnDoe,
            'password_plaintext' => 'correctPASSWORD123',
            'date_created' => '2017-07-01 12:20:15',
            'billing_address' => 1,
            'shipping_address' => 1
        ]);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('users');
        $result = $newUser->save($this->connection);
        $this->assertFalse($result);
        $this->assertEquals(-1, $newUser->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('users');
        $this->assertEquals($rowCountBeforeInsert, $rowCountAfterInsert);
    }
    
    public function testSavingUserFailsWithNonexistentAddressId()
    {
        $newUser = new User();
        $newUser->exchangeArray([
            'name' => 'Jakub Stonoga',
            'email' => 'jakub@stonoga.pl',
            'password_plaintext' => 'correctPASSWORD123',
            'date_created' => '2017-07-01 12:20:15',
            'billing_address' => 434,
            'shipping_address' => 546
        ]);
        $rowCountBeforeInsert = $this->getConnection()->getRowCount('users');
        $result = $newUser->save($this->connection);
        $this->assertFalse($result);
        $this->assertEquals(-1, $newUser->getId());
        $rowCountAfterInsert = $this->getConnection()->getRowCount('users');
        $this->assertEquals($rowCountBeforeInsert, $rowCountAfterInsert);
    }
    
    public function testUseCase()
    {
        //only to show how to use User
        
        //registration
        $dataFromRegistrationForm = [
            'email' => 'beata@marczak.pl',
            'password_plaintext' => 'correctPASSWORD123',
            'name' => 'Beata Marczak'
        ];
        $dataFromDbAfterInsertingNewAddresses = [
            'billing_address' => 1,
            'shipping_address' => 2
        ];
        $dataIsValid = User::validate(
            $this->createMock(InputValidator::class),
            $dataFromRegistrationForm,
            ['email', 'password_plaintext', 'name']
        );
        if (false === $dataIsValid) {
            //invalid data
            return;
        }
        
        $newUser = new User();
        $newUser->exchangeArray($dataFromRegistrationForm);
        $newUser->exchangeArray($dataFromDbAfterInsertingNewAddresses);
        $newUser->setDateCreated(date('Y-m-d H:i:s'));
        $newUser->save($this->connection);
        $newUser = null;
        
        //login
        $dataFromLoginForm = [
            'email' => 'beata@marczak.pl',
            'password_plaintext' => 'correctPASSWORD123'
        ];
        $dataIsValid = User::validate(
            $this->createMock(InputValidator::class),
            $dataFromLoginForm,
            ['email', 'password_plaintext']
        );
        if (false === $dataIsValid) {
            //invalid data
            return;
        }
        
        $newUser = User::loadUserByColumn(
            $this->connection,
            'email',
            $dataFromLoginForm['email']
        );
        $result = $newUser->authenticate(
            $dataFromLoginForm['email'],
            $dataFromLoginForm['password_plaintext']
        );
        if ($result) {
            //success
        } else {
            //failure
        }
    }
}
