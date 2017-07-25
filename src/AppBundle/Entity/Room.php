<?php

namespace AppBundle\Entity;

class Room extends AbstractModel
{
    private $number;
    private $description;
    private $note;

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * returns all components to this room
     */
    public function GetComponents()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponenten WHERE k_id = ?;");
        $query->bind_param("i", $this->getId());
        $query->execute();

        $result = $connection->query($query);
        $components = [];
        while ($row = $result->fetch_assoc()) {
            $component = new Component();
            $component->setId($row["k_id"]);
            $component->setRoomId($row["raeume_r_id"]);
            $component->setSupplierID($row["lieferant_l_id"]);
            $component->setPurchaseDate($row["k_einkaufsdatum"]);
            $component->setWarrantyDuration($row["k_gewaehrleistungsdauer"]);
            $component->setNote($row["k_notiz"]);
            $component->setProducer($row["k_hersteller"]);
            $component->setComponentTypeId($row["komponentenarten_ka_id"]);

            $components[] = $component;
        }

        return $components;
    }
}