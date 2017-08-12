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
        
        //temporary measure before creating categories table in db
        $category = new Category();
        $category->setId($categoryId);
        switch ($categoryId) {
            case 1:
                $category->setName('Flour');
                $category->setDescription('Various kinds of flour');
                break;
            case 2:
                $category->setName('Pasta');
                $category->setDescription('Various kinds of pasta');
                break;
            case 3:
                $category->setName('Rice');
                $category->setDescription('Various kinds of rice');
                break;
            case 4:
                $category->setName('Oils');
                $category->setDescription('Various kinds of oil');
                break;
            case 5:
                $category->setName('Sweeteners');
                $category->setDescription('Various kinds of sweeteners');
                break;
            case 6:
                $category->setName('Juices');
                $category->setDescription('Various kinds of juices');
                break;
            case 7:
                $category->setName('Vegetables');
                $category->setDescription('Various kinds of vegetables');
                break;
        }

        $counter = 1;
        for ($i = 0; $i < 12;  $i++) {
            $product = Product::showProductById($this->connection, $counter);
            $counter++;
            if ($counter > 3) {
                $counter = 1;
            }
            $category->addProduct($product);
        }
        
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