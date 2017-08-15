<?php
require_once __DIR__ . '/JsonPageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../Product.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';

class AddToCartController extends JsonPageController
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
        
        $productId = $postArray['id'];
        $isIdValid = is_numeric($productId)
            && intval($productId) == $productId
            && $productId > 0;
        if (false === $isIdValid) {
            return $this->returnError('Invalid product id');
        }
        
        $product = Product::showProductById($this->connection, $productId);
        if (false === $product instanceof Product) {
            return $this->returnError('Could not find product');
        }
        
        $manager = ShoppingManagerFactory::create($this->connection);
        $basket = $manager->loadOrCreateBasketByUser($user);
        $basket->addProducts($product, 1);
        $result = $manager->save($basket);
        
        if (false === $result) {
            return $this->returnError('Failed to add product');
        }
        
        return [
            'result' => json_encode($basket)
        ];
    }
}