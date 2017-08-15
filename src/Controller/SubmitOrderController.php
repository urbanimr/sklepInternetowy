<?php
require_once __DIR__ . '/JsonPageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';
require_once __DIR__ . '/../Order.php';
require_once __DIR__ . '/../OrderStatus.php';

class SubmitOrderController extends JsonPageController
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
        
        $noProductsInBasket = $basket->countProducts() < 1;
        if ($noProductsInBasket) {
            return $this->returnError('Cannot submit empty basket');
        }
        
        $manager->setNewStatusFor($basket, OrderStatus::STATUS_SUBMITTED);
        $result = $manager->save($basket);
        
        if (false === $result) {
            return $this->returnError('Failed to submit order');
        }
        
        $to = $user->getEmail();
        $subject = 'Order confirmation';
        $message = 'Your order has been submitted!';
        $headers = 'From: ' . parent::STORE_EMAIL
            . "\r\n" . 'Reply-To: ' . parent::STORE_EMAIL
            . "\r\n" . 'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
        
        return [
            'result' => json_encode(['code' => 1, 'error' => ''])
        ];
    }
}