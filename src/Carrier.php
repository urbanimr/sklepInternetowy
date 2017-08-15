<?php
require_once __DIR__ . '/../src/TableRow.php';

class Carrier implements TableRow, JsonSerializable
{
    const CARRIER_PICKUP = 1;
    const CARRIER_DHL = 2;
    const CARRIER_POCZTEX = 3;
    const CARRIER_SIODEMKA = 4;
    
    private $id;
    private $carrierName;
    private $description;
    private $price;
    private $active;
    
    /**
     * Only an outline. Ideally, carrier should use different strategies of
     * calculating cost through polymorphism.
     */
    public function calculateShippingCost(Order $basket)
    {
        return $this->price;
    }
    
    public function jsonSerialize()
    {
        $array = $this->exportArray();
        $array['id'] = $this->getId();
        return $array;
    }
    
    public function importArray(array $data)
    {
        $this->setId(
            isset($data['id'])
                ? $data['id']
                : $this->id
        );
        $this->setCarrierName(
            isset($data['carrier_name'])
                ? $data['carrier_name']
                : $this->carrierName
        );
        $this->setDescription(
            isset($data['description'])
                ? $data['description']
                : $this->description
        );
        $this->setPrice(
            isset($data['price'])
                ? $data['price']
                : $this->price
        );
        $this->setActive(
            isset($data['active'])
                ? $data['active']
                : $this->active
        );
    }

    public function exportArray()
    {
        return [
            'carrier_name' => $this->carrierName,
            'description' => $this->description,
            'price' => $this->price,
            'active' => $this->active,
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCarrierName()
    {
        return $this->carrierName;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setCarrierName(string $carrierName)
    {
        $this->carrierName = $carrierName;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
    }
}