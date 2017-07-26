<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 09:49
 * 
 * Provides functions to modify component type entities in
 * the database.
 * 
 */

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\AbstractEntity;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\ManagedConnection;

class ComponentTypeRepository
{
    /**
     * Gets a component by its primary key.
     * @param $id
     * @return ComponentType
     * @throws \Exception
     */
    public static function getComponentTypeById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenarten WHERE ka_id = ?;");

        $roomId = 0;

        $query->bind_param("i", $roomId);

        $roomId = $id;

        $query->execute();

        $result = $query->get_result();
        $query->close();
        $row = $result->fetch_assoc();

        if($row == NULL)
        {
            return NULL;
        }

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
     * Obtains a list of all component types.
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
     * Creates a component type in the database using
     * the provided Component Type object.
     * @param ComponentType $componentType
     * @return int|string
     * @throws \Exception
     */
    public static function createComponentType(ComponentType $componentType)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("INSERT INTO komponentenarten(ka_komponentenart) VALUES (?);");

        $type = 0;

        $query->bind_param("s", $type);

        $type = $componentType->getType();

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Erstellen der Komponentenart fehlgeschlagen");
        }

        return mysqli_insert_id($connection);
    }

    /**
     * Updates a component type in the database
     * using a php object.
     * @param ComponentType $componentType
     * @throws \Exception
     */
    public static function updateComponentType(ComponentType $componentType)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("UPDATE komponentenarten SET ka_komponentenart = ? WHERE ka_id = ?;");

        $type = 0;
        $id = 0;

        $query->bind_param("si", $type, $id);

        $type = $componentType->getType();
        $id = $componentType->getId();

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Ändern der Komponentenart fehlgeschlagen");
        }
    }

    /**
     * Deletes a component type from the database via id.
     * @param type $id
     * @throws \Exception
     */
    public static function deleteComponentTypeById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("DELETE FROM komponentenarten WHERE ka_id = ?;");

        $componentId = 0;

        $query->bind_param("i", $componentId);

        $componentId = $id;

        $query->execute();
        $query->close();

        if($connection->error)
        {
            throw new \Exception("Löschen der Komponentenart fehlgeschlagen");
        }
    }
}