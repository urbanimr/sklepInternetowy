<?php
require_once __DIR__ . '/JsonPageController.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../InputValidator.php';

class SignInController extends JsonPageController
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
        $postString = file_get_contents("php://input");
        $postArray = json_decode($postString, true); //null if not valid json
        if (!isset($postArray)) {
            return $this->returnError('Invalid input');
        }
        
        $input = [
            'email' => isset($postArray['email']) ? $postArray['email'] : '',
            'password' => isset($postArray['password']) ? $postArray['password'] : ''
        ];
        $requiredFields = ['email', 'password'];
        $isInputValid = User::validate(
            new InputValidator(),
            $input,
            $requiredFields
        );
        if (false === $isInputValid) {
            return $this->returnError('Invalid email or password');
        }
        
        $user = User::loadUserByColumn(
            $this->connection,
            'email',
            $input['email']
        );
        if (false === $user instanceof User) {
            return $this->returnError('Invalid email or password');
        }
        
        $isAuthSuccessful = $user->authenticate(
            $input['email'],
            $input['password']
        );
        if (false === $isAuthSuccessful) {
            return $this->returnError('Invalid email or password');
        }
        
        session_start();
        $_SESSION['userId'] = $user->getId();
        
        return [
            'result' => json_encode($user)
        ];
    }
}