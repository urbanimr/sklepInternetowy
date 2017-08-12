<?php
require_once __DIR__ . '/JsonPageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';
require_once __DIR__ . '/../Order.php';

class UpdateDetailsController extends JsonPageController
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

        $postString = file_get_contents("php://input");
        $postArray = json_decode($postString, true); //null if not valid json
        if (!isset($postArray)) {
            return $this->returnError('Invalid input');
        }
        
        $property =
            isset($postArray['property'])
            ? $postArray['property']
            : '';
        $value =
            isset($postArray['value'])
            ? $postArray['value']
            : '';
        
        $allowedProperties = [
            'billing_address',
            'shipping_address',
            'carrier_id',
            'payment_id',
            'comment'
        ];
        $isPropertyValid = in_array($property, $allowedProperties);
        if (false === $isPropertyValid) {
            return $this->returnError('Invalid property');
        }

        $isValueValid = Order::validate(
            new InputValidator(),
            [$property => $value],
            [$property]
        );
        if (false === $isValueValid) {
            return $this->returnError('Invalid value');
        }
        
        $manager = ShoppingManagerFactory::create($this->connection);
        $basket = $manager->loadOrCreateBasketByUser($user);
        $basket->importArray([$property => $value]);
        $result = $manager->save($basket);
        
        if (false === $result) {
            return $this->returnError('Failed to update property');
        }
        
        return [
            'result' => json_encode($basket)
        ];
    }
}