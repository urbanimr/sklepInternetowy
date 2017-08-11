<?php
/**
 * parent of all page controllers
 */
abstract class PageController
{
    const SITE_NAME = 'Online store';
    const STORE_EMAIL = 'notification@online-store.pl';
    /**
     * @var PDO connection to DB
     */    
    protected $connection;
    /**
     * @var string Page to be displayed, e.g. 'delete.php' 
     */
    protected $page;
    /**
     * @var array Array of data to be displayed in view, 'e.g. ['title' => 'Godfather']
     */
    protected $outputData;
    
    public function __construct()
    {
        $this->setConnection();
        $this->outputData = [];
        $this->addOutputData($this->defaultOutput());
        $this->addOutputData($this->customAction());
    }
   
    /**
     * Sets the connection to DB
     * @return null|string Null if success, string with error if error 
     */
    protected function setConnection()
    {
        require_once __DIR__ . '/../../config/connection.php';
        $this->connection = $conn;
    }
    
    /**
     * @return PDO connection to DB
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string Page to be displayed, e.g. 'books.php'
     */
    function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $page Page to be displayed, e.g. 'books.php'
     */
    function setPage(string $page)
    {
        $this->page = $page;
    }
    
    public function defaultOutput()
    {
        return [
            'pageTitle' => self::SITE_NAME
        ];
    }

    /**
     * custom action performed by individual controllers. It has to set $this->page property and return values to be displayed in view
     * @return array Array of data to be displayed in view, 'e.g. ['title' => 'Godfather']
     */
    abstract protected function customAction();
    
    /**
     * @return ViewModel The ViewModel object that displays the view page
     */
    public function display()
    {
        require_once __DIR__ . '/../ViewModel.php';
        return new ViewModel($this->page, $this->outputData);
    }
    
    /**
     * The array given as parameter will be merged to $this->data
     * @param array $array Array of data to be displayed in view, 'e.g. ['title' => 'Godfather']
     */
    protected function addOutputData(array $array)
    {
        $this->outputData = array_merge($this->outputData, $array);
    }
}