<?php
require_once __DIR__ . '/../src/Product.php';

class CatalogProductsGateway
{
    private $conn;
    
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }
    
    public function loadProductById($id)
    {
        return Product::showProductById($this->conn, $id);
    }
}