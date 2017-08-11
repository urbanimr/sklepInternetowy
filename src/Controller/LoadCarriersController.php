<?php
require_once __DIR__ . '/PageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';

class LoadCarriersController extends PageController
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
        
        $carriersGateway = new CarriersGateway($this->connection);
        $carriers = $carriersGateway->loadActiveCarriers();
        
        return [
            'result' => json_encode($carriers)
        ];
    }
    
    private function returnError($error)
    {
        return [
            'result' => json_encode(['code' => 0, 'error' => $error])
        ];
    }
}