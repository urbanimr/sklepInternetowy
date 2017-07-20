<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Order.php';

class ShoppingManager
{
    private $ordersGateway;
    private $carriersGateway;
    private $orderStatusesGateway;
    
    public function __construct(
        OrdersGateway $ordersGateway,
        CarriersGateway $carriersGateway,
        OrderStatusesGateway $orderStatusesGateway
    ) {
        $this->ordersGateway = $ordersGateway;
        $this->carriersGateway = $carriersGateway;
        $this->orderStatusesGateway = $orderStatusesGateway;
    }
    
    public function loadOrCreateBasketByUser(User $user)
    {
        $basketStatus = $this->orderStatusesGateway->loadBasketStatusByUserId(
            $user->getId()
        );
        
        $basketNotExistsYet = (false == $basketStatus instanceof OrderStatus);
        if ($basketNotExistsYet) {
            return $this->createNewBasket($user);
        }
        
        $basket = $this->ordersGateway->loadOrderByColumn(
            'id',
            $basketStatus->getOrderId()
        );
        
        $basketShouldExistButNotLoaded = (false == $basket instanceof Order);
        if ($basketShouldExistButNotLoaded) {
            throw new RuntimeException('Could not load basket from database');
        }
        
        return $basket;
    }
    
    private function createNewBasket(User $user)
    {
        $basket = new Order($this->carriersGateway);
        
        $userInfo = [
            'user_id' => $user->getId(),
            'billing_address' => $user->getBillingAddressId(),
            'shipping_address' => $user->getShippingAddressId(),
        ];
        $basket->importArray($userInfo);
        
        $this->setNewStatusFor($basket, OrderStatus::STATUS_BASKET);
        
        return $basket;
    }
    
    public function loadSubmittedOrdersByUser(User $user)
    {
        return $this->ordersGateway->loadSubmittedOrdersByUser($user);
    }
    
    public function loadRecentOrders(int $limit, int $offset = 0)
    {
        return $this->ordersGateway->loadRecentOrders($limit, $offset);
    }
    
    public function setNewStatusFor(Order $order, int $status)
    {
        $lastStatus = $order->getLastStatus();
        
        if (isset($lastStatus)) {
            $newStatus = $lastStatus->createNewStatus($status);
        } else {
            $newStatus = new OrderStatus();
            $basketStatusValues = [
                'status_id' => OrderStatus::STATUS_BASKET,
                'status_name' => OrderStatus::STATUS_NAME_BASKET,
                'order_id' => $order->getId(),
                'date' => date('Y-m-d H:i:s')
            ];
            $newStatus->importArray($basketStatusValues);
        }
        
        $order->addStatus($newStatus);
    }
    
    public function save(Order $order)
    {
        $this->ordersGateway->save($order);
    }
}