<?php
require_once __DIR__ . '/PageController.php';
require_once __DIR__ . '/../Product.php';

class ProductController extends PageController
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
        $this->setPage('json.php');
        
        $productId = isset($_GET['id']) ? $_GET['id'] : '';
        
        $isIdValid = is_numeric($productId)
            && intval($productId) == $productId
            && $productId > 0;
        if (false === $isIdValid) {
            $this->debug = 'id invalid';
            return $this->returnProductNotFoundError();
        }
        
        $product = Product::showProductById($this->getConnection(), $productId);
        if (false === $product instanceof Product) {
            $this->debug = 'product not loaded';
            return $this->returnProductNotFoundError();
        }
        
        switch($product->getId()) {
            case 1:
                $product->images[0] = 'mydlo.jpg';
                break;
            case 2:
                $product->images[0] = 'szydlo.jpg';
                break;
            case 3:
                $product->images[0] = 'powidlo.jpg';
                break;
        }
        
        return [
            'result' => json_encode($product)
        ];
    }
    
    private function returnProductNotFoundError()
    {
        return [
            'result' => json_encode(['code' => 0, 'error' => 'Product not found'])
        ];
    }
}