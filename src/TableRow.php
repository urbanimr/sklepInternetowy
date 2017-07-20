<?php
interface TableRow
{
    public function getId();
    
    public function setId(int $id);
    
    public function importArray(array $array);
    
    public function exportArray();
}