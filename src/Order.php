<?php
require_once __DIR__ . '/../src/OrderStatus.php';
require_once __DIR__ . '/../src/OrderProduct.php';
require_once __DIR__ . '/../src/Carrier.php';
require_once __DIR__ . '/../src/Product.php';

class Order implements TableRow
{
    //constant below are temporary and should be moved to class Payment
    const PAYMENT_CASH = 1;
    const PAYMENT_TRANSFER = 2;
    const PAYMENT_CHEQUE = 3;
    
    private $id;
    private $userId;
    private $billingAddress;
    private $shippingAddress;
    private $carrier;
    private $payment;
    private $comment;
    private $shippingCost;
    private $totalAmount;
    private $products;
    private $statuses;
    
    private $carriersGateway;

    public function __construct(CarriersGateway $carriersGateway)
    {
        $this->carriersGateway = $carriersGateway;
        
        $this->id = -1;
        $this->userId = -1;
        $this->billingAddress = -1;
        $this->shippingAddress = -1;
        $this->shippingCost = 0.00;
        $this->payment = self::PAYMENT_CASH;
        $this->comment = '';
        $this->totalAmount = 0.00;
        
        $this->products = [];
        $this->statuses = [];
        
        //Carrier should be set last as it requires and/or overwrites some of the previous actions
        $this->setCarrier(Carrier::CARRIER_PICKUP);
    }
    
    public function addProducts(Product $product, int $quantity)
    {
        $orderProduct = $this->getOrderProductById($product->getId());

        if (!isset($orderProduct)) {
            $this->appendToProducts($product, $quantity);
        } else {
            $this->increaseQuantity($orderProduct, $quantity);
        }
        
        $this->calculateTotalAmount();
    }
    
    private function appendToProducts(Product $product, int $quantity)
    {
        $newOrderProduct = new OrderProduct();
        $data = [
            'id' => -1,
            'order_id' => $this->getId(),
            'product_id' => $product->getId(),
            'quantity' => $quantity,
            'price' => $product->getPrice(),
        ];
        $newOrderProduct->importArray($data);
        $this->products[] = $newOrderProduct;
    }
    
    private function increaseQuantity(OrderProduct $product, int $quantity)
    {
        $product->setQuantity($product->getQuantity() + $quantity);
    }
    
    public function removeProducts(Product $product, int $quantity)
    {
        $orderProduct = $this->getOrderProductById($product->getId());
        
        if (!isset($orderProduct)) {
            return;
        }
        
        $previousQuantity = $orderProduct->getQuantity();
        $orderProduct->setQuantity($previousQuantity - $quantity);
        
        $this->calculateTotalAmount();
    }
    
    public function clearProducts()
    {
        foreach ($this->getOrderProducts() as $product) {
            $product->setQuantity(0);
        }
        
        $this->calculateTotalAmount();
    }
    
    //this method is only used to load products from db when the order has already been submitted
    //it loads products without updating prices
    public function setProductsWithOldPrices(array $products)
    {
        foreach ($products as $product) {
            if (false === $product instanceof OrderProduct) {
                throw new LogicException('all array items should be OrderProducts');
            }
            $this->products = $products;
        }
    }
    
    private function calculateTotalAmount()
    {
        $total = 0;
        foreach ($this->getOrderProducts() as $product) {
            $total .= $product->getQuantity() * $product->getPrice();
        }
        $total += $this->getShippingCost();
        $this->setTotalAmount($total);
    }
    
    public function getOrderProducts()
    {
        return $this->products;
    }
    
    public function getOrderProductById(int $searchedId)
    {
        foreach ($this->products as $product) {
            if ($product->getProductId() == $searchedId) {
                return $product;
            }
        }
        return null;
    }
    
    public function countProducts()
    {
        $total = 0;
        foreach ($this->products as $product) {
            $total += $product->getQuantity();
        }
        return $total;
    }
    
    /**
     * for updating data
     * @param array $data e.g. ['name' => 'Jan Kowalski']
     */
    public function importArray(array $data)
    {
        $this->setId(
            isset($data['id'])
                ? $data['id']
                : $this->id
        );
        $this->setUserId(
            isset($data['user_id'])
                ? $data['user_id']
                : $this->userId
        );
        $this->setBillingAddress(
            isset($data['billing_address'])
                ? $data['billing_address']
                : $this->billingAddress
        );
        $this->setShippingAddress(
            isset($data['shipping_address'])
                ? $data['shipping_address']
                : $this->shippingAddress
        );
        $this->setPayment(
            isset($data['payment_id'])
                ? $data['payment_id']
                : $this->payment
        );
        $this->setComment(
            isset($data['comment'])
                ? $data['comment']
                : $this->comment
        );
        $this->setShippingCost(
            isset($data['shipping_cost'])
                ? $data['shipping_cost']
                : $this->shippingCost
        );
        $this->setTotalAmount(
            isset($data['total_amount'])
                ? $data['total_amount']
                : $this->totalAmount
        );
        
        //carrier should be last to set
        $this->setCarrier(
            isset($data['carrier_id'])
                ? $data['carrier_id']
                : $this->carrier
        );
    }
    
    public function exportArray()
    {
        return [
            'user_id' => $this->getUserId(),
            'billing_address' => $this->getBillingAddress(),
            'shipping_address' => $this->getShippingAddress(),
            'carrier_id' => $this->getCarrier(),
            'payment_id' => $this->getPayment(),
            'comment' => $this->getComment(),
            'shipping_cost' => $this->getShippingCost(),
            'total_amount' => $this->getTotalAmount()
        ];
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
        
        if (isset($data['user_id'])) {
            $validator->addValidations([
                'user_id' => [
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
        
        if (isset($data['carrier_id'])) {
            $validator->addValidations([
                'carrier_id' => [
                    ['isInt'],
                    ['greaterThan', 0]
                ]
            ]);
        }
        
        if (isset($data['payment_id'])) {
            $validator->addValidations([
                'payment_id' => [
                    ['isInt'],
                    ['greaterThan', 0]
                ]
            ]);
        }

        if (isset($data['comment'])) {
            $validator->addValidations([
                'comment' => [
                    ['notEmpty'],
                    ['shorterThan', 255]
                ]
            ]);
        }
        
        if (isset($data['shipping_cost'])) {
            $validator->addValidations([
                'shipping_cost' => [
                    ['isNumeric'],
                    ['greaterThanOrEqual',0]
                ]
            ]);
        }
        
        if (isset($data['total_amount'])) {
            $validator->addValidations([
                'total_amount' => [
                    ['isNumeric'],
                    ['greaterThanOrEqual',0]
                ]
            ]);
        }
        
        return $validator->validate();
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }
    
    public function setBillingAddress(int $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    public function setShippingAddress(int $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }
    
    public function setCarrier(int $carrier)
    {
        $this->carrier = $carrier;
        
        $isBasket = null !== $this->getLastStatus()
            && $this->getLastStatus()->getStatusId() == OrderStatus::STATUS_BASKET;
        
        if ($isBasket) {
            $this->calculateShippingCost();
            $this->calculateTotalAmount();
        }
    }
    
    private function calculateShippingCost()
    {
        $loadedCarrier = $this->carriersGateway->loadCarrierById(
            $this->getCarrier()
        );
        $this->setShippingCost($loadedCarrier->getPrice());
    }
    
    public function setPayment(int $payment)
    {
        $this->payment = $payment;
    }

    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }
    
    public function setShippingCost(float $shippingCost)
    {
        $this->shippingCost = $shippingCost;
    }
    
    public function setTotalAmount(float $totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }
    
    public function addStatus(OrderStatus $status)
    {
        $this->statuses[] = $status;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }
    
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    public function getCarrier()
    {
        return $this->carrier;
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function getComment()
    {
        return $this->comment;
    }
    
    public function getShippingCost()
    {
        return $this->shippingCost;
    }
    
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }
    
    public function getLastStatus()
    {
        if (empty($this->statuses)) {
            return null;
        }
        
        return $this->getStatuses()[count($this->getStatuses()) - 1];
    }
    
    public function getStatuses()
    {
        return $this->statuses;
    }
}