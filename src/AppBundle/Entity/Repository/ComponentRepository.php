<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 10:32
 */

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\Component;
use AppBundle\Entity\ManagedConnection;

class ComponentRepository
{
    /**
     * @param Component $component
     * @throws \Exception
     */
    public static function createComponent(Component $component)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("INSERT INTO komponenten(raeume_r_id, lieferant_l_id, k_einkaufsdatum, k_gewaehrleistungsdauer, k_notiz, k_hersteller, komponentenarten_ka_id) VALUES (?, ?, ?, ?, ?, ?, ?);");

        $roomId = 0;
        $supplierId = 0;
        $purchaseDate = 0;
        $warrantyDuration = 0;
        $note = 0;
        $producer = 0;
        $componentTypeId = 0;

        $query->bind_param("iidissi", $roomId, $supplierId, $purchaseDate, $warrantyDuration, $note, $producer, $componentTypeId);

        $roomId = $component->getRoomId();
        $supplierId = $component->getSupplierId();
        $purchaseDate = $component->getPurchaseDate();
        $warrantyDuration = $component->getWarrantyDuration();
        $note = $component->getNote();
        $producer = $component->getProducer();
        $componentTypeId = $component->getComponentTypeId();

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Erstellen der Komponente fehlgeschlagen");
        }
    }

    /**
     * @param Component $component
     * @throws \Exception
     */
    public static function updateComponent(Component $component)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("UPDATE komponenten SET raeume_id = ?, lieferant_l_id = ?, k_einkaufsdatum = ?, k_gewaehrleistungsdauer = ?, k_notiz = ?, k_hersteller = ?, komponentenarten_ka_id = ? WHERE k_id = ?;");

        $roomId = 0;
        $supplierId = 0;
        $purchaseDate = 0;
        $warrantyDuration = 0;
        $note = 0;
        $producer = 0;
        $componentTypeId = 0;
        $componentId = 0;

        $query->bind_param("iidissii", $roomId, $supplierId, $purchaseDate, $warrantyDuration, $note, $producer, $componentTypeId, $componentId);

        $roomId = $component->getRoomId();
        $supplierId = $component->getSupplierId();
        $purchaseDate = $component->getPurchaseDate();
        $warrantyDuration = $component->getWarrantyDuration();
        $note = $component->getNote();
        $producer = $component->getProducer();
        $componentTypeId = $component->getComponentTypeId();
        $componentId = $component->getId();

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Ändern der Komponente fehlgeschlagen");
        }
    }
}