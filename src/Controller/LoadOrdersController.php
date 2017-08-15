<?php
require_once __DIR__ . '/JsonPageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';

class LoadOrdersController extends JsonPageController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * custom action performed by individual controllers. It has to set the $this->page property and return values to be displayed in view
     * @return array Array of data to be displayed in view, 'e.g. ['title' => 'Godfather']
     */
    protected function customJsonAction()
    {
        session_start();
        if (!isset($_SESSION['userId'])) {
            return $this->returnError('Sign in to start shopping');
        }
        
        $user = User::loadUserByColumn(
            $this->connection,
            'id',
            $_SESSION['userId']
        );
        if (false === $user instanceof User) {
            return $this->returnError('User could not be found');
        }        
        
        $manager = ShoppingManagerFactory::create($this->connection);
        $orders = $manager->loadSubmittedOrdersByUser($user);
         
        if (!isset($_GET['id'])) {
            return [
                'result' => json_encode($orders)
            ];
        }
        
        $orderId = $_GET['id'];
        $isIdValid = is_numeric($orderId)
            && intval($orderId) == $orderId
            && $orderId > 0;
        if (false === $isIdValid) {
            return $this->returnError('Invalid order id');
        }
        
        foreach($orders as $order) {
            if ($order->getId() == $orderId) {
                $requestedOrder = $order;
                break;
            }
        }
        
        if (!isset($requestedOrder)) {
            return $this->returnError('Invalid order id');
        }

        return [
            'result' => json_encode($requestedOrder)
        ];
    }
}