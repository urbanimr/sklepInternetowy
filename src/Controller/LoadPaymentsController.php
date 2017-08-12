<?php
require_once __DIR__ . '/JsonPageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';
require_once __DIR__ . '/../ShoppingManagerFactory.php';

class LoadPaymentsController extends JsonPageController
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
        
        //temporary measure
        $payments = [
            [
                'id' => 1,
                'payment_name' => 'Cash',
                'description' => 'Pay with cash upon receival'
            ],
            [
                'id' => 2,
                'payment_name' => 'Bank transfer',
                'description' => 'Pay with bank transfer'
            ],
            [
                'id' => 3,
                'payment_name' => 'Cheque',
                'description' => 'Pay with cheque'
            ]
        ];
        
        return [
            'result' => json_encode($payments)
        ];
    }
}