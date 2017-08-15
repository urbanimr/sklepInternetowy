<?php

class Address implements JsonSerializable
{
    private $id;
    private $alias;
    private $company;
    private $name;
    private $address1;
    private $address2;
    private $postcode;
    private $city;
    private $country;
    private $phone;
    private $taxNo;
        
    public function __construct()
    {
        $this->id = -1;
        $this->alias = '';
        $this->company = '';
        $this->name = '';
        $this->address1 = '';
        $this->address2 = '';
        $this->postcode = '';
        $this->city = '';
        $this->country = '';
        $this->phone = '';
        $this->taxNo = '';
    }

    public static function loadAddressByColumn(PDO $conn, string $column, $value)
    {
        $stmt = $conn->prepare("SELECT * FROM addresses WHERE $column = :$column LIMIT 1");
        $result = $stmt->execute([$column => $value]);
        if ($result == true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedAddress = new Address();
            $loadedAddress->exchangeArray($row);
            return $loadedAddress;
        }
        return null;
    }
    
    /**
     * for updating data
     * @param array $data e.g. ['name' => 'Jan Kowalski']
     */
    public function exchangeArray($data)
    {
        $this->setId(
            isset($data['id']) ? $data['id'] : $this->id
        );
        $this->setAlias(
            isset($data['alias']) ? $data['alias'] : $this->alias
        );
        $this->setCompany(
            isset($data['company']) ? $data['company'] : $this->company
        );
        $this->setName(
            isset($data['name']) ? $data['name'] : $this->name
        );
        $this->setAddress1(
            isset($data['address1']) ? $data['address1'] : $this->address1
        );
        $this->setAddress2(
            isset($data['address2']) ? $data['address2'] : $this->address2
        );
        $this->setPostcode(
            isset($data['postcode']) ? $data['postcode'] : $this->postcode
        );
        $this->setCity(
            isset($data['city']) ? $data['city'] : $this->city
        );
        $this->setCountry(
            isset($data['country']) ? $data['country'] : $this->country
        );
        $this->setPhone(
            isset($data['phone']) ? $data['phone'] : $this->phone
        );
        $this->setTaxNo(
            isset($data['tax_no']) ? $data['tax_no'] : $this->taxNo
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
        
        $completeSql = "INSERT INTO addresses ($columnsList) VALUES ($paramsList)";
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
        
        $completeSql = "UPDATE addresses SET $paramsList WHERE id=:id";
        $stmt = $conn->prepare($completeSql);
        $reverseExchangeArray['id'] = $this->getId();
        $result = $stmt->execute($reverseExchangeArray);
        
        return $result;
    }
    
    public function jsonSerialize()
    {
        $array = $this->getReverseExchangeArray();
        $array['id'] = $this->getId();
        return $array;
    }
    
    private function getReverseExchangeArray()
    {
        return [
            'alias' => $this->getAlias(),
            'company' => $this->getCompany(),
            'name' => $this->getName(),
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'postcode' => $this->getPostcode(),
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
            'phone' => $this->getPhone(),
            'tax_no' => $this->getTaxNo()
        ];
    }

    
    public function delete(PDO $conn)
    {
        if ($this->getId() == -1) {
            return false;
        }

        $stmt = $conn->prepare('DELETE FROM addresses WHERE id=:id LIMIT 1');
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

        if (isset($data['alias'])) {
            $validator->addValidations([
                'alias' => [
                    ['notEmpty'],
                    ['shorterThan', 80]
                ]
            ]);
        }
        
        if (isset($data['company'])) {
            $validator->addValidations([
                'company' => [
                    ['notEmpty'],
                    ['shorterThan', 255]
                ]
            ]);
        }
        
        if (isset($data['name'])) {
            $validator->addValidations([
                'name' => [
                    ['notEmpty'],
                    ['shorterThan', 255],
                    ['personNamePattern']
                ]
            ]);
        }
        
        if (isset($data['address1'])) {
            $validator->addValidations([
                'address1' => [
                    ['notEmpty'],
                    ['shorterThan', 255]
                ]
            ]);
        }

        if (isset($data['address2'])) {
            $validator->addValidations([
                'address2' => [
                    ['notEmpty'],
                    ['shorterThan', 255]
                ]
            ]);
        }

        if (isset($data['postcode'])) {
            $validator->addValidations([
                'postcode' => [
                    ['notEmpty'],
                    ['shorterThan', 20]
                ]
            ]);
        }
        
        if (isset($data['city'])) {
            $validator->addValidations([
                'city' => [
                    ['notEmpty'],
                    ['shorterThan', 255]
                ]
            ]);
        }
        
        if (isset($data['country'])) {
            $validator->addValidations([
                'country' => [
                    ['notEmpty'],
                    ['shorterThan', 255]
                ]
            ]);
        }
        
        if (isset($data['phone'])) {
            $validator->addValidations([
                'phone' => [
                    ['notEmpty'],
                    ['shorterThan', 20]
                ]
            ]);
        }
        
        if (isset($data['tax_no'])) {
            $validator->addValidations([
                'tax_no' => [
                    ['notEmpty'],
                    ['shorterThan', 20]
                ]
            ]);
        }
        
        return $validator->validate();
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getAlias()
    {
        return $this->alias;
    }
    
    public function getCompany()
    {
        return $this->company;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getAddress1()
    {
        return $this->address1;
    }
    
    public function getAddress2()
    {
        return $this->address2;
    }
    
    public function getPostcode()
    {
        return $this->postcode;
    }
    
    public function getCity()
    {
        return $this->city;
    }
    
    public function getCountry()
    {
        return $this->country;
    }
    
    public function getPhone()
    {
        return $this->phone;
    }
    
    public function getTaxNo()
    {
        return $this->taxNo;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    
    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setTaxNo($taxNo)
    {
        $this->taxNo = $taxNo;
    }
}
