<?php

class Category implements JsonSerializable
{
    private $id;
    private $name;
    private $description;
    private $products;

    public function __construct($id = -1, $name = '', $description = '')
    {
        $this->id = -1;
        $this->setName($name);
        $this->setDescription($description);
        $this->products = [];
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'products' => $this->getProducts()
        ];
    }

    /**
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        if(is_string($name)) {
            $this->name = $name;
        }
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        if(is_string($description)) {
            $this->description = $description;
        }
    }
    
    public function addProduct(Product $product)
    {
        $this->products[] = $product;
    }
    
    public function getProducts()
    {
        return $this->products;
    }
}

