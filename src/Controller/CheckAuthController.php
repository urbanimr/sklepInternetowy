<?php
require_once __DIR__ . '/JsonPageController.php';

class CheckAuthController extends JsonPageController
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
        $code = isset($_SESSION['userId']) ? 1 : 0;
        
        return [
            'result' => json_encode(['code' => $code, 'error' => ''])
        ];
    }
}