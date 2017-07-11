<?php

/**
 * Created by PhpStorm.
 * User: marcinurbaniak
 * Date: 02.07.2017
 * Time: 15:40
 */
class Product
{
    private $id;
    private $name;
    private $price;
    private $description;
    private $quantity;




    public function __construct($id = null, $name = '',$price = 0, $quantity = 0, $description = '')
    {
        //$this->id = -1;
        $this->setName($name);
        $this->setPrice($price);
        $this->setDescription($description);
        $this->setQuantity($quantity);

    }




    public function sellProduct($quantity)
    {
        if($this->quantity >= $quantity)
        {
            $this->quantity -= $quantity;
        }
    }


    public function buyProduct($quantity)
    {
        if($quantity > 0){
            $this->quantity += $quantity;
        }
    }


    public function uploadProductToDataBase(PDO $conn, $name,$price = 0,$quantity = 0, $description =''){
        $this->setName($name);
        $this->setPrice($price);
        $this->setQuantity($quantity);
        $this->setDescription($description);

        $sql = 'INSERT INTO products(name, price, description, quantity) VALUES (:name,:price,:description,:quantity)';

        try{
            $stmt = $conn->prepare($sql);
            $stmt->execute(['name'=>$name,'price'=>$price, 'description' =>$description, 'quantity' =>$quantity]);
        } catch (PDOException $exception){
            echo $exception->getMessage();
        }

    }

    static public function showProductById(PDO $conn, $id){
        $sql = 'SELECT * FROM products WHERE id=:id';

        try{
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute(['id'=>$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $exception){
            echo $exception->getMessage();
        }

    }

    static public function showAllProductsName(PDO $conn){

        $sql = 'SELECT name FROM products';
        $arr = [];

        $result = $conn->query($sql);
        if($result !== false && $result->rowCount()>0){
            foreach ($result as $row){
                array_push($arr,$row['name']);
            }
        }

        return $arr;

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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        if($price > 0) {
            $this->price = $price;
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


    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        if($quantity>=0) {
            $this->quantity = $quantity;
        }
    }
    

}



