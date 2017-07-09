<?php

class User
{
    private $id;
    private $name;
    private $email;
    private $hashPass;
    private $dateCreated;
    private $addresses;
    
    public function __construct()
    {
        $this->id = -1;
        $this->name = '';
        $this->email = '';
        $this->hashPass = '';
        $this->dateCreated = time();
        $this->addresses = [];
    }
    
    static public function loadUserByColumn(PDO $conn, string $column, $value)
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE $column = :$column LIMIT 1");
        $result = $stmt->execute([$column => $value]);
        if ($result == true && $stmt->rowCount() > 0 ) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new User();
            $loadedUser->exchangeArray($row);        
            return $loadedUser;
        }
        return null;
    }
    
    static public function loadManyUsers(
        PDO $conn,
        int $limit = 25,
        int $offset = 0,
        string $orderBy = 'date_created',
        bool $isOrderAsc = false
    )
    {
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
    public function exchangeArray($data)
    {
        $this->setId(isset($data['id']) ? $data['id'] : $this->id);
        $this->setName(isset($data['name']) ? $data['name'] : $this->name);
        $this->setEmail(isset($data['email']) ? $data['email'] : $this->email);
        $this->setDateCreated(isset($data['date_created']) ? $data['date_created'] : $this->dateCreated);
        if (isset($data['password'])) {
            $this->setHashPass($data['password']);
        } else if (isset($data['password_plaintext'])) {
            $this->setPassword($data['password_plaintext']);
        }
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
            'date_created' => $this->getDateCreated()
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
    )
    {
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
        
        return $validator->validate();
    }
    
    public function authenticate($email, $plainTextPassword)
    {        
        $emailCorrect = $email == $this->email;
        $passwordCorrect = password_verify($plainTextPassword, $this->hashPass);

        return $emailCorrect && $passwordCorrect;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function setPassword($plainTextPassword)
    {
        $hashPass = password_hash($plainTextPassword, PASSWORD_BCRYPT);
        $this->setHashPass($hashPass);
    }
    
    public function setHashPass($hashPass)
    {
        $this->hashPass = $hashPass;
    }
    
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
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
}