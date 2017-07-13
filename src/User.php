<?php

class User
{
    private $id;
    private $name;
    private $email;
    private $hashPass;
    private $dateCreated;
    private $billingAddressId;
    private $billingAddress;
    private $shippingAddressId;
    private $shippingAddress;
    
    public function __construct()
    {
        $this->id = -1;
        $this->name = '';
        $this->email = '';
        $this->hashPass = '';
        $this->dateCreated = time();
        $this->billingAddressId = -1;
        $this->billingAddress = null;
        $this->shippingAddressId = -1;
        $this->shippingAddress = null;
    }
    
    public static function loadUserByColumn(PDO $conn, string $column, $value)
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE $column = :$column LIMIT 1");
        $result = $stmt->execute([$column => $value]);
        if ($result == true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new User();
            $loadedUser->exchangeArray($row);
            return $loadedUser;
        }
        return null;
    }
    
    public static function loadManyUsers(
        PDO $conn,
        int $limit = 25,
        int $offset = 0,
        string $orderBy = 'date_created',
        bool $isOrderAsc = false
    ) {
        //Uwaga! $offset = 1 oznacza, że pierwszy wczytany wpis będzie mieć id 2
        $orderByExpression = "ORDER BY $orderBy" . ' ';
        $orderByExpression .= $isOrderAsc ? 'ASC' : 'DESC';
        $limitExpression = "LIMIT $limit";
        if ($offset != 0) {
            $limitExpression .= ' ' . "OFFSET $offset";
        }
        $completeQuery = 'SELECT * FROM users'
            . ' '
            . $orderByExpression
            . ' '
            . $limitExpression;

        $returnArray = [];
        $result = $conn->query($completeQuery);
        if ($result !== false && $result->rowCount() != 0) {
            foreach ($result as $row) {
                $loadedUser = new User();
                $loadedUser->exchangeArray($row);
                $returnArray[] = $loadedUser;
            }
        }
        return $returnArray;
    }
    
    /**
     * for updating data. Password can be provided in two forms
     * the hashed form overwrites the plain text form
     * @param array $data e.g. ['name' => 'Jan Kowalski']
     */
    public function exchangeArray(array $data)
    {
        $this->setId(isset($data['id']) ? $data['id'] : $this->id);
        $this->setName(isset($data['name']) ? $data['name'] : $this->name);
        $this->setEmail(isset($data['email']) ? $data['email'] : $this->email);
        $this->setDateCreated(isset($data['date_created']) ? $data['date_created'] : $this->dateCreated);
        if (isset($data['password'])) {
            $this->setHashPass($data['password']);
        } elseif (isset($data['password_plaintext'])) {
            $this->setPassword($data['password_plaintext']);
        }
        $this->setBillingAddressId(
            isset($data['billing_address'])
                ? $data['billing_address']
                : $this->billingAddressId
        );
        $this->setShippingAddressId(
            isset($data['shipping_address'])
                ? $data['shipping_address']
                : $this->shippingAddressId
        );
    }
    
    public function save(PDO $conn)
    {
        if ($this->getId() == -1) {
            return $this->insert($conn);
        }
        
        return $this->update($conn);
    }
    
    private function insert(PDO $conn)
    {
        $reverseExchangeArray = $this->getReverseExchangeArray();
        
        $columnNamesArray = array_keys($reverseExchangeArray);
        $columnsList = implode(', ', $columnNamesArray);
        
        $paramNamesArray = array_map(
            function ($columnName) {
                return ':' . $columnName;
            },
            $columnNamesArray
        );
        $paramsList = implode(', ', $paramNamesArray);
        
        $completeSql = "INSERT INTO users ($columnsList) VALUES ($paramsList)";
        $stmt = $conn->prepare($completeSql);
        $result = $stmt->execute($reverseExchangeArray);
        
        if ($result === false) {
            return false;
        }
        
        $this->setId($conn->lastInsertId());
        return true;
    }

    private function update(PDO $conn)
    {
        $reverseExchangeArray = $this->getReverseExchangeArray();
        $columnNamesArray = array_keys($reverseExchangeArray);
        $paramNamesArray = array_map(
            function ($columnName) {
                return $columnName . '=:' . $columnName;
            },
            $columnNamesArray
        );
        $paramsList = implode(', ', $paramNamesArray);
        
        $completeSql = "UPDATE users SET $paramsList WHERE id=:id";
        $stmt = $conn->prepare($completeSql);
        $reverseExchangeArray['id'] = $this->getId();
        $result = $stmt->execute($reverseExchangeArray);
        
        return $result;
    }
    
    private function getReverseExchangeArray()
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'password' => $this->getHashPass(),
            'date_created' => $this->getDateCreated(),
            'billing_address' => $this->getBillingAddressId(),
            'shipping_address' => $this->getShippingAddressId()
        ];
    }
    
    public function delete(PDO $conn)
    {
        if ($this->getId() == -1) {
            return false;
        }

        $stmt = $conn->prepare('DELETE FROM users WHERE id=:id LIMIT 1');
        $result = $stmt->execute(['id' => $this->getId()]);
        
        if ($result !== true) {
            return false;
        }
        
        $this->setId(-1);
        return true;
    }
    
    /**
     * @param InputValidator $validator
     * @param array $data e.g. ['name' => 'Jan Kowalski']
     * @param array $requiredFields e.g. ['name', 'email', 'dateCreated']
     * @return boolean True if required data is present and all data is valid
     */
    public static function validate(
        InputValidator $validator,
        array $data,
        array $requiredFields
    ) {
        for ($i = 0; $i < count($requiredFields); $i++) {
            if (!isset($data[$requiredFields[$i]])) {
                return false;
            }
        }
        
        $validator->clear();
        $validator->addInput($data);
        
        if (isset($data['id'])) {
            $validator->addValidations([
                'id' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }

        if (isset($data['name'])) {
            $validator->addValidations([
                'name' => [
                    ['notEmpty'],
                    ['personNamePattern']
                ]
            ]);
        }
        
        if (isset($data['email'])) {
            $validator->addValidations([
                'email' => [
                    ['notEmpty'],
                    ['emailPattern']
                ]
            ]);
        }
        
        if (isset($data['password'])) {
            $validator->addValidations([
                'email' => [
                    ['notEmpty'],
                    ['passwordPattern']
                ]
            ]);
        }
        
        if (isset($data['dateCreated'])) {
            $validator->addValidations([
                'dateCreated' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        if (isset($data['billing_address'])) {
            $validator->addValidations([
                'billing_address' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        if (isset($data['shipping_address'])) {
            $validator->addValidations([
                'shipping_address' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        return $validator->validate();
    }
    
    public function authenticate(string $email, string $plainTextPassword)
    {
        $emailCorrect = $email == $this->email;
        $passwordCorrect = password_verify($plainTextPassword, $this->hashPass);

        return $emailCorrect && $passwordCorrect;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
    
    public function setPassword(string $plainTextPassword)
    {
        $hashPass = password_hash($plainTextPassword, PASSWORD_BCRYPT);
        $this->setHashPass($hashPass);
    }
    
    public function setHashPass(string $hashPass)
    {
        $this->hashPass = $hashPass;
    }
    
    public function setDateCreated(string $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }
    
    public function setBillingAddressId(int $billingAddressId)
    {
        $this->billingAddressId = $billingAddressId;
    }
    
    public function setBillingAddress(Address $address)
    {
        $this->billingAddress = $address;
    }
    
    public function setShippingAddressId(int $shippingAddressId)
    {
        $this->shippingAddressId = $shippingAddressId;
    }

    public function setShippingAddress(Address $address)
    {
        $this->shippingAddress = $address;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function getHashPass()
    {
        return $this->hashPass;
    }
    
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    
    public function getAddresses()
    {
        return $this->addresses;
    }
    
    public function getBillingAddressId()
    {
        return $this->billingAddressId;
    }
    
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }
    
    public function getShippingAddressId()
    {
        return $this->shippingAddressId;
    }
    
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
}
