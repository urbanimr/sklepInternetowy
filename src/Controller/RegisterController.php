<?php
require_once __DIR__ . '/PageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../Address.php';
require_once __DIR__ . '/../InputValidator.php';

class RegisterController extends PageController
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

        $postString = file_get_contents("php://input");
        $postArray = json_decode($postString, true); //null if not valid json
        if (!isset($postArray)
                || !isset($postArray['user'])
                || !isset($postArray['billingAddress'])) {
            return $this->returnError('Invalid input');
        }
        
        $userInput = $postArray['user'];
        $userRequiredFields = ['email', 'password_plaintext'];
        $isUserInputValid = User::validate(
            new InputValidator(),
            $userInput,
            $userRequiredFields
        );
        if (false === $isUserInputValid) {
            return $this->returnError('Invalid user input');
        }
        
        $billingAddressInput = $postArray['billingAddress'];
        $billingAddressRequiredFields = [
            'alias',
            'name',
            'address1',
            'postcode',
            'city',
            'country',
            'phone'
            ];
        $isBillingAddressInputValid = Address::validate(
            new InputValidator(),
            $billingAddressInput,
            $billingAddressRequiredFields
        );
        if (false === $isBillingAddressInputValid) {
            return $this->returnError('Invalid billing address input');
        }
        
        $emailExists = User::loadUserByColumn(
            $this->connection,
            'email',
            $userInput['email']
        );
        if ($emailExists) {
            return $this->returnError('Email already exists');
        }
        
        $address = new Address();
        $address->exchangeArray($billingAddressInput);
        $address->save($this->connection);
        if ($address->getId() == -1) {
            return $this->returnError('Could not save address, registration failed');
        }
        
        $user = new User();
        $user->exchangeArray($userInput);
        $user->setBillingAddressId($address->getId());
        $user->setShippingAddressId($address->getId());
        $user->setDateCreated(date('Y-m-d H:i:s'));
        $user->save($this->connection);
        if ($user->getId() == -1) {
            return $this->returnError('Could not save user, registration failed');
        }

        return [
            'result' => json_encode(['code' => 1, 'error' => ''])
        ];
    }
    
    private function returnError($error)
    {
        return [
            'result' => json_encode(['code' => 0, 'error' => $error])
        ];
    }
}