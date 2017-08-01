<?php
require_once __DIR__ . '/PageController.php';
require_once __DIR__ . '/../Product.php';
require_once __DIR__ . '/../Category.php';

class CategoryController extends PageController
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
        
        $categoryId = isset($_GET['id']) ? $_GET['id'] : '';
        
        $isIdValid = is_numeric($categoryId)
            && intval($categoryId) == $categoryId
            && $categoryId > 0;
        if (false === $isIdValid) {
            $this->debug = 'id invalid';
            return $this->returnCategoryNotFoundError();
        }
        
        $category = new Category();
        $category->setName('Category 1');
        $category->setDescription('This is a description of category 1');
        $counter = 1;
        for ($i = 0; $i < 12;  $i++) {
            $product = Product::showProductById($this->connection, $counter);
            $counter++;
            if ($counter > 3) {
                $counter = 1;
            }
            $category->addProduct($product);
        }
        
//        $product = Product::showProductById($this->getConnection(), $productId);
//        if (false === $product instanceof Product) {
//            $this->debug = 'product not loaded';
//            return $this->returnCategoryNotFoundError();
//        }
        
        return [
            'result' => json_encode($category)
        ];
    }
    
    private function returnCategoryNotFoundError()
    {
        return [
            'result' => json_encode(['code' => 0, 'error' => 'Category not found'])
        ];
    }
}