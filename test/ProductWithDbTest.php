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
    
//    public function testLoadUserByColumn()
//    {
//        $john = User::loadUserByColumn($this->connection, 'id', 1);
//        $this->assertInstanceOf(User::class, $john);
//        $this->assertEquals(1, $john->getId());
//        $this->assertEquals('John Doe', $john->getName());
//        $this->assertEquals('john@doe.com', $john->getEmail());
//        $this->assertTrue($john->authenticate('john@doe.com', 'doe'));
//        $this->assertEquals('2017-07-01 12:20:15', $john->getDateCreated());
//    }
//    
//    public function testLoadManyUsersLoadsUserObjects()
//    {
//        $users = User::loadManyUsers($this->connection);
//        $this->assertInternalType('array', $users);
//        $this->assertInstanceOf(User::class, $users[0]);
//        $this->assertNotEquals($users[0]->getId(), $users[1]->getId());
//    }
//    
//    public function testLoadManyUsersWithDefaultParamsLoadsUsersSortedByDateDescending()
//    {
//        $users = User::loadManyUsers($this->connection);
//        $dateValues = [];
//        foreach($users as $user) {
//            $dateValues[] = strtotime($user->getDateCreated());
//        }
//        $dateValuesInExpectedOrder = $dateValues;
//        rsort($dateValuesInExpectedOrder);
//        $this->assertSame($dateValuesInExpectedOrder, $dateValues);
//    }
//    
//    public function testLoadManyUsersReturnsCorrectSetOfUsers()
//    {
//        $largerGroupOfUsers = User::loadManyUsers(
//            $this->connection,
//            4,
//            0,
//            'date_created',
//            false
//        );
//        $smallerGroupOfUsers = User::loadManyUsers(
//            $this->connection,
//            2,
//            1,
//            'date_created',
//            false
//        );
//        $this->assertCount(4, $largerGroupOfUsers);
//        $this->assertCount(2, $smallerGroupOfUsers);
//        $this->assertEquals(
//            $largerGroupOfUsers[1]->getId(),
//            $smallerGroupOfUsers[0]->getId()
//        );
//        $this->assertEquals(
//            $largerGroupOfUsers[2]->getId(),
//            $smallerGroupOfUsers[1]->getId()
//        );
//    }
//    
//    public function testDeleteUserWithPersistedUserUpdatesDbChangesIdAndReturnsTrue()
//    {
//        $idOfJohn = 1;
//        $john = User::loadUserByColumn($this->connection, 'id', $idOfJohn);
//        $this->assertInstanceOf(User::class, $john);
//        $rowCountBeforeDelete = $this->getConnection()->getRowCount('users');
//        $result = $john->delete($this->connection);
//        $this->assertTrue($result);
//        $this->assertEquals(-1, $john->getId());
//        $this->assertNull(User::loadUserByColumn($this->connection, 'id', $idOfJohn));
//        $rowCountAfterDelete = $this->getConnection()->getRowCount('users');
//        $rowCountDifference = $rowCountBeforeDelete - $rowCountAfterDelete;
//        $this->assertEquals(1, $rowCountDifference);
//    }
//    
//    public function testDeleteUserWithNonPersistedUserOnlyReturnsFalse()
//    {
//        $newUser = new User();
//        $newUser->exchangeArray([
//            'name' => 'Jakub Stonoga',
//            'email' => 'jakub@stonoga.pl',
//            'password_plaintext' => 'correctPASSWORD123',
//            'date_created' => '2017-07-01 12:20:15'
//        ]);
//        $rowCountBeforeDelete = $this->getConnection()->getRowCount('users');
//        $result = $newUser->delete($this->connection);
//        $this->assertFalse($result);
//        $this->assertEquals(-1, $newUser->getId());
//        $rowCountAfterDelete = $this->getConnection()->getRowCount('users');
//        $this->assertEquals($rowCountBeforeDelete, $rowCountAfterDelete);
//    }
//    
//    public function testSaveWithNonPersistedUserInsertsRowChangesIdReturnsTrue()
//    {
//        $newUser = new User();
//        $newUser->exchangeArray([
//            'name' => 'Jakub Stonoga',
//            'email' => 'jakub@stonoga.pl',
//            'password_plaintext' => 'correctPASSWORD123',
//            'date_created' => '2017-07-01 12:20:15'
//        ]);
//        $rowCountBeforeInsert = $this->getConnection()->getRowCount('users');
//        $result = $newUser->save($this->connection);
//        $this->assertTrue($result);
//        $this->assertNotEquals(-1, $newUser->getId());
//        $rowCountAfterInsert = $this->getConnection()->getRowCount('users');
//        $rowCountDifference = $rowCountAfterInsert - $rowCountBeforeInsert;
//        $this->assertEquals(1, $rowCountDifference);    
//    }
//    
//    public function testSaveWithPersistedUseOnlyUpdatesRowAndReturnsTrue()
//    {
//        $idOfJohn = 1;
//        $john = User::loadUserByColumn($this->connection, 'id', $idOfJohn);
//        $rowCountBeforeUpdate = $this->getConnection()->getRowCount('users');
//        $newValues = [
//            'name' => 'Jakub Stonoga',
//            'email' => 'jakub@stonoga.pl',
//            'password_plaintext' => 'correctPASSWORD123',
//            'date_created' => '2017-07-01 12:20:15'
//        ];
//        $john->exchangeArray($newValues);
//        $result = $john->save($this->connection);
//        $this->assertTrue($result);
//        $this->assertEquals($idOfJohn, $john->getId());
//        $rowCountAfterUpdate = $this->getConnection()->getRowCount('users');
//        $this->assertEquals($rowCountBeforeUpdate, $rowCountAfterUpdate);
//        $john = null;
//        $newJohn = User::loadUserByColumn($this->connection, 'id', $idOfJohn);
//        $this->assertEquals($newValues['name'], $newJohn->getName());
//    }
//    
//    public function testSavingUserWithTheSameEmailNotPossible()
//    {
//        $emailOfJohnDoe = 'john@doe.com';
//        $newUser = new User();
//        $newUser->exchangeArray([
//            'name' => 'Jakub Stonoga',
//            'email' => $emailOfJohnDoe,
//            'password_plaintext' => 'correctPASSWORD123',
//            'date_created' => '2017-07-01 12:20:15'
//        ]);
//        $rowCountBeforeInsert = $this->getConnection()->getRowCount('users');
//        $result = $newUser->save($this->connection);
//        $this->assertFalse($result);
//        $this->assertEquals(-1, $newUser->getId());
//        $rowCountAfterInsert = $this->getConnection()->getRowCount('users');
//        $this->assertEquals($rowCountBeforeInsert, $rowCountAfterInsert);   
//    }
//    
//    public function testUseCase()
//    {
//        //only to show how to use User
//        
//        //registration
//        $dataFromRegistrationForm = [
//            'email' => 'beata@marczak.pl',
//            'password_plaintext' => 'correctPASSWORD123',
//            'name' => 'Beata Marczak'
//        ];
//        $dataIsValid = User::validate(
//            $this->createMock(InputValidator::class),
//            $dataFromRegistrationForm,
//            ['email', 'password_plaintext', 'name']
//        );
//        if (false === $dataIsValid) {
//            //invalid data
//            return;
//        }
//        
//        $newUser = new User();
//        $newUser->exchangeArray($dataFromRegistrationForm);
//        $newUser->setDateCreated(date('Y-m-d H:i:s'));
//        $newUser->save($this->connection);
//        
//        //login
//        $dataFromLoginForm = [
//            'email' => 'beata@marczak.pl',
//            'password_plaintext' => 'correctPASSWORD123'           
//        ];
//        $dataIsValid = User::validate(
//            $this->createMock(InputValidator::class),
//            $dataFromLoginForm,
//            ['email', 'password_plaintext']
//        );
//        if (false === $dataIsValid) {
//            //invalid data
//            return;
//        }
//        
//        $newUser = User::loadUserByColumn(
//            $this->connection,
//            'email',
//            $dataFromLoginForm['email']
//        );
//        $result = $newUser->authenticate(
//            $dataFromLoginForm['email'],
//            $dataFromLoginForm['password_plaintext']
//        );
//        if ($result) {
//            //success
//        } else {
//            //failure
//        }
//    }
}