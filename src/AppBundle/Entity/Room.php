<?php

namespace AppBundle\Entity;

class Room extends AbstractEntity implements ValidatingEntity {
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
    public function getComponents()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponenten WHERE k_id = ?;");

        $id = 0;

        $query->bind_param("i", $id);

        $id = $this->getId();

        $query->execute();

        $result = $query->get_result();
        $query->close();
        $components = [];
        while ($row = $result->fetch_assoc())
        {
            $component = new Component();
            $component->setId($row["k_id"]);
            $component->setRoomId($row["raeume_r_id"]);
            $component->setSupplierId($row["lieferant_l_id"]);
            $component->setPurchaseDate($row["k_einkaufsdatum"]);
            $component->setWarrantyDuration($row["k_gewaehrleistungsdauer"]);
            $component->setNote($row["k_notiz"]);
            $component->setProducer($row["k_hersteller"]);
            $component->setComponentTypeId($row["komponentenarten_ka_id"]);
            $component->setName($row["k_kennung"]);

            $components[] = $component;
        }

        return $components;
    }

    public function isValid() {
        if ($this->number === null || $this->number === "") {
            return false;
        }

        return true;
    }
}