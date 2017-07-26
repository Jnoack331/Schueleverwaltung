<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 09:49
 */

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\AbstractEntity;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\ManagedConnection;

class ComponentTypeRepository
{
    /**
     * @param $id
     * @return ComponentType
     * @throws \Exception
     */
    public static function getComponentTypeById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenarten WHERE ka_id = ?;");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $connection->query($query);
        $row = $result->fetch_row();

        $componentType = new ComponentType();
        $componentType->setId($row["ka_id"]);
        $componentType->setType($row["ka_komponentenart"]);

        if($connection->error)
        {
            throw new \Exception("Selektieren der Komponentenart fehlgeschlagen");
        }

        return $componentType;
    }

    /**
     * @return array
     * @throws \Exception
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
     * @return int|string
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

        return mysqli_insert_id($connection);
    }

    /**
     * @param ComponentType $componentType
     * @throws \Exception
     */
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

    public static function deleteComponentTypeById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("DELETE FROM komponentenarten WHERE ka_id = ?;");
        $query->bind_param("i", $id);
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Löschen der Komponentenart fehlgeschlagen");
        }
    }
}