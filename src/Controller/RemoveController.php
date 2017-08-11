<?php
require_once __DIR__ . '/PageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../Product.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';

class UpdateQtyController extends PageController
{
    private $debug;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * custom action performed by individual controllers. It has to set the $this->page property and return values to be displayed in view
     * @return array Array of data to be displayed in view, 'e.g. ['title' => 'Godfather']
     */
    protected function customAction()
    {
//        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
//            http_response_code(404);
//            die();
//        }
        
        $this->setPage('json.php');
        
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
        
        $catalogProductId =
            isset($postArray['catalogProduct']['id'])
            ? $postArray['catalogProduct']['id']
            : '';
        
        $isIdValid = is_numeric($catalogProductId)
            && $catalogProductId > 0;
        $isQuantityValid = is_numeric($newQuantity)
            && $newQuantity >= 0;
        $areRelevantValuesValid = $isIdValid && $isQuantityValid;
        if (false === $areRelevantValuesValid) {
            return $this->returnError('Invalid product values');
        }
                
        $product = Product::showProductById($this->connection, $catalogProductId);
        if (false === $product instanceof Product) {
            return $this->returnError('Could not find product');
        }
        
        $manager = ShoppingManagerFactory::create($this->connection);
        $basket = $manager->loadOrCreateBasketByUser($user);
        $previousQuantity = $basket->getOrderProductById($catalogProductId)
            ->getQuantity();
        $quantityDifference = $newQuantity - $previousQuantity;
        if ($quantityDifference > 0) {
            $basket->addProducts($product, $quantityDifference);
        } else if ($quantityDifference < 0) {
            $basket->removeProducts($product, abs($quantityDifference));
        }
        
        $result = $manager->save($basket);
        
        if (false === $result) {
            return $this->returnError('Failed to update quantity');
        }
        
        return [
            'result' => json_encode($basket)
        ];
    }
    
    private function returnError($error)
    {
        return [
            'result' => json_encode(['code' => 0, 'error' => $error])
        ];
    }
}