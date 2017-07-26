<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 10:32
 */

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\Component;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\ManagedConnection;

class ComponentRepository
{
    /**
     * @return array
     * @throws \Exception
     */
    public static function getAllComponents()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = "SELECT * FROM komponenten;";
        $result = $connection->query($query);

        $components = [];

        while($row = $result->fetch_assoc())
        {
            $component = new Component();
            $component->setId($row["k_id"]);
            $component->setNote($row["k_notiz"]);
            $component->setComponentTypeId($row["komponentenarten_ka_id"]);
            $component->setProducer($row["k_hersteller"]);
            $component->setWarrantyDuration($row["k_gewaehrleistungsdauer"]);
            $component->setPurchaseDate($row["k_einkaufsdatum"]);
            $component->setRoomId($row["raeume_r_id"]);
            $component->setSupplierId($row["lieferant_l_id"]);
            $component->setName($row["k_kennung"]);
            $components[] = $component;
        }

        if($connection->error)
        {
            throw new \Exception("Selektieren der Komponenten fehlgeschlagen");
        }

        return $components;
    }

    /**
     * @param $id
     * @return Component
     * @throws \Exception
     */
    public static function getComponentById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponenten WHERE k_id = ?;");

        $componentId = 0;

        $query->bind_param("i", $componentId);

        $componentId = $id;

        $query->execute();
        $result = $query->get_result();
        $query->close();

        $row = $result->fetch_assoc();

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

        if($connection->error)
        {
            throw new \Exception("Erstellen der Komponente fehlgeschlagen");
        }

        return $component;
    }

    /**
     * @param Component $component
     * @return int|string
     * @throws \Exception
     */
    public static function createComponent(Component $component)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("INSERT INTO komponenten(raeume_r_id, lieferant_l_id, k_einkaufsdatum, k_gewaehrleistungsdauer, k_notiz, k_hersteller, komponentenarten_ka_id, k_kennung) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");

        $roomId = 0;
        $supplierId = 0;
        $purchaseDate = date_create();
        $warrantyDuration = 0;
        $note = 0;
        $producer = 0;
        $componentTypeId = 0;
        $name = 0;

        $query->bind_param("iisissis", $roomId, $supplierId, $purchaseDate, $warrantyDuration, $note, $producer, $componentTypeId, $name);

        $roomId = $component->getRoomId();
        $supplierId = $component->getSupplierId();
        $purchaseDate = date_format($component->getPurchaseDate(), "Y-m-d");
        $warrantyDuration = $component->getWarrantyDuration();
        $note = $component->getNote();
        $producer = $component->getProducer();
        $componentTypeId = $component->getComponentTypeId();
        $name = $component->getName();

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Erstellen der Komponente fehlgeschlagen");
        }

        return mysqli_insert_id($connection);
    }

    /**
     * @param Component $component
     * @throws \Exception
     */
    public static function updateComponent(Component $component)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("UPDATE komponenten SET raeume_id = ?, lieferant_l_id = ?, k_einkaufsdatum = ?, k_gewaehrleistungsdauer = ?, k_notiz = ?, k_hersteller = ?, komponentenarten_ka_id = ?, k_kennung = ? WHERE k_id = ?;");

        $roomId = 0;
        $supplierId = 0;
        $purchaseDate = 0;
        $warrantyDuration = 0;
        $note = 0;
        $producer = 0;
        $componentTypeId = 0;
        $componentId = 0;
        $name = 0;

        $query->bind_param("iidissisi", $roomId, $supplierId, $purchaseDate, $warrantyDuration, $note, $producer, $componentTypeId, $name, $componentId);

        $roomId = $component->getRoomId();
        $supplierId = $component->getSupplierId();
        $purchaseDate = $component->getPurchaseDate();
        $warrantyDuration = $component->getWarrantyDuration();
        $note = $component->getNote();
        $producer = $component->getProducer();
        $componentTypeId = $component->getComponentTypeId();
        $componentId = $component->getId();
        $name = $component->getName();

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Ändern der Komponente fehlgeschlagen");
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public static function deleteComponentById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("DELETE FROM komponenten WHERE k_id = ?;");

        $componentId = 0;

        $query->bind_param("i", $componentId);

        $componentId = $id;

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Ändern der Komponente fehlgeschlagen");
        }
    }
}