<?php
abstract class TableGateway
{
    protected $conn;
    protected $table;
    
    public function __construct(PDO $conn, string $table)
    {
        $this->conn = $conn;
        $this->table = $table;
    }

//    example of joinedTables:
//        $joinedTables = [
//            'statuses' => [
//                'joinColumns' => ['status_id', 'id'],
//                'selectedColumns' => ['status_name']
//            ]
//        ];
    //Uwaga! $offset = 1 oznacza, że pierwszy wczytany wpis będzie mieć id 2
    protected function loadItemsByColumn(
        string $whereColumn,
        $whereValue,
        array $joinedTables,
        int $limit = 100,
        int $offset = 0,
        string $orderBy = 'date',
        bool $isOrderAsc = true
    ) {
        $selectJoinWhereSql = $this->prepareSelectJoinWhereSql(
            $whereColumn, $joinedTables
        );
        
        $limitOrderBySql = $this->prepareLimitOrderBySql(
            $limit, $offset, $orderBy, $isOrderAsc
        );
        
        $completeQuery = $selectJoinWhereSql . ' ' . $limitOrderBySql;

        $returnArray = [];
        
        $stmt = $this->conn->prepare($completeQuery);
        $result = $stmt->execute([$whereColumn => $whereValue]);
        if ($result == true && $stmt->rowCount() > 0) {
            foreach ($stmt as $row) {
                $loadedItem = $this->createItemFromRow($row);
                $returnArray[] = $loadedItem;
            }            
        }
        if ($limit == 1) {
            return (count($returnArray) > 0) ? $returnArray[0] : null;
        }
        
        return $returnArray;  
    }
    
    private function prepareSelectJoinWhereSql(
        string $column, array $joinedTables
    ) {
        $joinedColumnsArray = [];
        $joinedPrefixedColumnsArray = [];
        $joinsArray = [];
        foreach ($joinedTables as $table => $columns) {
            foreach ($columns['selectedColumns'] as $selectedColumn) {
                $joinedColumnsArray[] = $selectedColumn;
                $joinedPrefixedColumnsArray[] = $table . '.' . $selectedColumn;
            }
            $joinsArray[] = "JOIN $table"
                . " ON "
                . $this->table . '.' . $columns['joinColumns'][0]
                . " = "
                . "$table.{$columns['joinColumns'][1]}";
        }
        
        $columnsToSelect = $this->table . '.*';
        if (count($joinedPrefixedColumnsArray) > 0) {
            $columnsToSelect .= ', ' . implode(', ', $joinedPrefixedColumnsArray);
        }
        
        $joins = '';
        if (count($joinsArray) > 0) {
            $joins = ' ' . implode(' ', $joinsArray) . ' ';
        }
        
        $fullColumnName = in_array($column, $joinedColumnsArray)
            ? $column
            : $this->table . '.' . $column;
        
        $sql = "SELECT $columnsToSelect"
            . " ". "FROM $this->table"
            . $joins;
        
        if (!empty($column)) {
            $sql .= " " . "WHERE $fullColumnName = :$column";
        }
        
        return $sql;
    }
    
    private function prepareLimitOrderBySql(
        int $limit = 25,
        int $offset = 0,
        string $orderBy = 'id',
        bool $isOrderAsc = true
    ) {
        $orderByExpression = "ORDER BY $orderBy" . ' ';
        $orderByExpression .= $isOrderAsc ? 'ASC' : 'DESC';
        $limitExpression = "LIMIT $limit";
        if ($offset != 0) {
            $limitExpression .= ' ' . "OFFSET $offset";
        }
        return $orderByExpression . ' ' . $limitExpression;
    }
    
    abstract protected function createItemFromRow($row);
    
    protected function insertItem(TableRow $item)
    {
        $exportArray = $item->exportArray();
        
        $columnNamesArray = array_keys($exportArray);
        $columnsList = implode(', ', $columnNamesArray);
        
        $paramNamesArray = array_map(
            function ($columnName) {
                return ':' . $columnName;
            },
            $columnNamesArray
        );
        $paramsList = implode(', ', $paramNamesArray);
        
        $completeSql = "INSERT INTO {$this->table} ($columnsList)"
            . ' '
            . "VALUES ($paramsList)";
        
        $stmt = $this->conn->prepare($completeSql);
        $result = $stmt->execute($exportArray);
        
        if ($result === false) {
            return false;
        }
        
        $item->setId($this->conn->lastInsertId());
        
        return true;
    }

    protected function updateItem(TableRow $item)
    {
        $exportArray = $item->exportArray();
        $columnNamesArray = array_keys($exportArray);
        $paramNamesArray = array_map(
            function ($columnName) {
                return $columnName . '=:' . $columnName;
            },
            $columnNamesArray
        );
        $paramsList = implode(', ', $paramNamesArray);
        
        $completeSql = "UPDATE {$this->table} SET $paramsList WHERE id=:id";
        $stmt = $this->conn->prepare($completeSql);
        $exportArray['id'] = $item->getId();
        $result = $stmt->execute($exportArray);
        
        return $result;
    }
    
    protected function deleteItem(TableRow $item)
    {
        if ($item->getId() == -1) {
            return false;
        }
        
        $sql = 'DELETE FROM'
            . ' '
            . $this->table
            . ' '
            . 'WHERE id=:id LIMIT 1';

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute(['id' => $item->getId()]);
        
        if ($result !== true) {
            return false;
        }
        
        $item->setId(-1);
        
        return true;
    }
}