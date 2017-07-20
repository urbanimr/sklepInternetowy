<?php
require_once __DIR__ . '/../src/TableGateway.php';
require_once __DIR__ . '/../src/Carrier.php';

class CarriersGateway extends TableGateway
{
    public function __construct(PDO $conn) {
        $table = 'carriers';
        parent::__construct($conn, $table);
    }
    
    public function loadCarrierById($id)
    {
        $joinedTables = [];
        
        $args = [
            'whereColumn' => 'id',
            'whereValue' => $id,
            'joinedTables' => $joinedTables,
            'limit' => 1,
            'offset' => 0,
            'orderBy' => 'id',
            'isOrderAsc' => true
        ];
        return $this->loadItemsByColumn(
            $args['whereColumn'],
            $args['whereValue'],
            $args['joinedTables'],
            $args['limit'],
            $args['offset'],
            $args['orderBy'],
            $args['isOrderAsc']
        );
    }
    
    public function loadAllCarriers()
    {
        $joinedTables = [];
        
        $args = [
            'whereColumn' => '',
            'whereValue' => '',
            'joinedTables' => $joinedTables,
            'limit' => 1000,
            'offset' => 0,
            'orderBy' => 'id',
            'isOrderAsc' => true
        ];
        return $this->loadItemsByColumn(
            $args['whereColumn'],
            $args['whereValue'],
            $args['joinedTables'],
            $args['limit'],
            $args['offset'],
            $args['orderBy'],
            $args['isOrderAsc']
        );
    }
    
    public function loadActiveCarriers()
    {
        $joinedTables = [];
        
        $args = [
            'whereColumn' => 'active',
            'whereValue' => '1',
            'joinedTables' => $joinedTables,
            'limit' => 1000,
            'offset' => 0,
            'orderBy' => 'id',
            'isOrderAsc' => true
        ];
        return $this->loadItemsByColumn(
            $args['whereColumn'],
            $args['whereValue'],
            $args['joinedTables'],
            $args['limit'],
            $args['offset'],
            $args['orderBy'],
            $args['isOrderAsc']
        );
    }
    
    protected function createItemFromRow($row)
    {
        $newItem = new Carrier();
        $newItem->importArray($row);
        return $newItem;
    }
}