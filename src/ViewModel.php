<?php
/**
 * called by page controller, displays the view file
 */
class ViewModel
{
    /**
     * An array provided by page controller with data that should be available in the view file
     * @var array e.g. ['title' => 'Krzyżacy']
     */
    protected $data;
    /**
     * @var string e.g. 'books.php' Filename of the view file to be displayed
     */
    protected $page;
    
    /**
     * @param string $page Filename, e.g. delete.php
     * @param array $data Data that should be available in the view page
     */
    public function __construct(string $page, array $data)
    {
        $this->setPage($page);
        $this->setData($data);
        include __DIR__ . '/View/' . $this->getPage();
    }
    
    /**
     * @return array Data to be displayed in view
     */
    function getData()
    {
        return $this->data;
    }

    /**
     * 
     * @return string e.g. 'books.php' Filename of the view file to be displayed
     */
    function getPage()
    {
        return $this->page;
    }

    /**
     * @param array $data
     */
    function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $page
     */
    function setPage(string $page)
    {
        $this->page = $page;
    }


}