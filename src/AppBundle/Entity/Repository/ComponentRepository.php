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
    public static function createComponent(Component $component)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("INSERT INTO komponenten(raeume_r_id, lieferant_l_id, k_einkaufsdatum, k_gewaehrleistungsdauer, k_notiz, k_hersteller, komponentenarten_ka_id) VALUES (?, ?, ?, ?, ?, ?, ?);");
        $query->bind_param("iidissi", $component->getRoomId(), $component->getSupplierId(), $component->getPurchaseDate(), $component->getWarrantyDuration(), $component->getNote(), $component->getProducer(), $component->getComponentTypeId());
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Erstellen der Komponente fehlgeschlagen");
        }
    }

    public static function updateComponent(Component $component)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("UPDATE komponenten SET raeume_id = ?, lieferant_l_id = ?, k_einkaufsdatum = ?, k_gewaehrleistungsdauer = ?, k_notiz = ?, k_hersteller = ?, komponentenarten_ka_id = ? WHERE k_id = ?;");
        $query->bind_param("iidissii", $component->getRoomId(), $component->getSupplierId(), $component->getPurchaseDate(), $component->getWarrantyDuration(), $component->getNote(), $component->getProducer(), $component->getComponentTypeId(), $component->getId());
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Ändern der Komponente fehlgeschlagen");
        }
    }
}