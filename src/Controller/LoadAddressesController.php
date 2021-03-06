<?php
require_once __DIR__ . '/JsonPageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../Address.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';

class LoadAddressesController extends JsonPageController
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
        $basket = $manager->loadOrCreateBasketByUser($user);
        if ($basket->getId() == -1) {
            $manager->save($basket);
        }
        
        $billingAddressId = $basket->getBillingAddress();
        $shippingAddressId = $basket->getShippingAddress();
        $addresses['billingAddress'] = Address::loadAddressByColumn($this->connection, 'id', $billingAddressId);
        $addresses['shippingAddress'] = Address::loadAddressByColumn($this->connection, 'id', $shippingAddressId);
        
        return [
            'result' => json_encode($addresses)
        ];
    }
}