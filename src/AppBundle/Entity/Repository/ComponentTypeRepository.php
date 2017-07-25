<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 09:49
 */

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\AbstractModel;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\ManagedConnection;

class ComponentTypeRepository
{
    /**
     * @return array
     */
    public static function getAllComponentTypes()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $result = $connection->query("SELECT * FROM komponentenarten");

        $componentTypes = [];

        while($row = $result->fetch_assoc())
        {
            $componentType = new ComponentType();
            $componentType->setId($row["ka_id"]);
            $componentType->setType($row["ka_komponentenart"]);
            $componentTypes[] = $componentType;
        }

        if($connection->error)
        {
            throw new \Exception("Selektierung der Komponentenarten fehlgeschlagen");
        }

        return $componentTypes;
    }

    /**
     * @param ComponentType $componentType
     * @throws \Exception
     */
    public static function createComponentType(ComponentType $componentType)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("INSERT INTO komponentenarten(ka_komponentenart) VALUES (?);");
        $query->bind_param("s", $componentType->getType());
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Erstellen der Komponentenart fehlgeschlagen");
        }
    }

    public static function updateComponentType(ComponentType $componentType)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("UPDATE komponentenarten SET ka_komponentenart = ? WHERE ka_id = ?;");
        $query->bind_param("si", $componentType->getType(), $componentType->getId());
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Ändern der Komponentenart fehlgeschlagen");
        }
    }
}