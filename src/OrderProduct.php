<?php
require_once __DIR__ . '/../src/TableRow.php';
require_once __DIR__ . '/../src/Product.php';

/**
 * represents one item from the list of products of a specific basket/order
 * links orders to products
 * keeps product price from the moment of submitting the order
 */
class OrderProduct implements TableRow, JsonSerializable
{
    private $id;
    private $orderId;
    private $productId;
    private $quantity;
    private $price;
    private $catalogProduct;
    
    public function __construct()
    {
        $this->id = -1;
        $this->orderId = -1;
        $this->productId = -1;
        $this->quantity = 0;
        $this->price = 0.00;
        $this->catalogProduct = null;
    }
    
    public function jsonSerialize()
    {
        $array = $this->exportArray();
        $array['id'] = $this->getId();
        $array['catalogProduct'] = $this->getCatalogProduct();
        return $array;
    }
    
    public function importArray(array $data)
    {
        $this->setId(
            isset($data['id'])
                ? $data['id']
                : $this->id
        );
        $this->setOrderId(
            isset($data['order_id'])
                ? $data['order_id']
                : $this->orderId
        );
        $this->setProductId(
            isset($data['product_id'])
                ? $data['product_id']
                : $this->productId
        );
        $this->setQuantity(
            isset($data['quantity'])
                ? $data['quantity']
                : $this->quantity
        );
        $this->setPrice(
            isset($data['price'])
                ? $data['price']
                : $this->price
        );
    }

    public function exportArray()
    {
        return [
            'order_id' => $this->orderId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
    }
    
    public function productIdEquals(int $expectedId)
    {
        return $this->productId == $expectedId;
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

        if (isset($data['order_id'])) {
            $validator->addValidations([
                'order_id' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        if (isset($data['product_id'])) {
            $validator->addValidations([
                'product_id' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        if (isset($data['quantity'])) {
            $validator->addValidations([
                'quantity' => [
                    ['isInt'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        if (isset($data['price'])) {
            $validator->addValidations([
                'price' => [
                    ['isFloat'],
                    ['greaterThan',0]
                ]
            ]);
        }
        
        return $validator->validate();
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function setProductId(int $productId)
    {
        $this->productId = $productId;
    }

    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function setPrice(float $price)
    {
        $this->price = $price;
    }
    
    public function setCatalogProduct(Product $catalogProduct)
    {
        $this->catalogProduct = $catalogProduct;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getPrice()
    {
        return $this->price;
    }
    
    public function getCatalogProduct()
    {
        return $this->catalogProduct;
    }
}
