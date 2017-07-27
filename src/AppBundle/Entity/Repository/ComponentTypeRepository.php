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
     * @return ComponentType|null
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
        if($query->error) {$query->close(); throw new \Exception("Selektieren der Komponentenart fehlgeschlagen");}
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

        $query = "SELECT * FROM komponentenarten;";
        $result = $connection->query($query);

        if($result === false)
        {
            throw new \Exception("Selektierung der Komponentenarten fehlgeschlagen");
        }

        $componentTypes = [];

        while($row = $result->fetch_assoc())
        {
            $componentType = new ComponentType();
            $componentType->setId($row["ka_id"]);
            $componentType->setType($row["ka_komponentenart"]);
            $componentTypes[] = $componentType;
        }

        return $componentTypes;
    }

    /**
     * @param $name
     * @return ComponentType|null
     * @throws \Exception
     */
    public static function getComponentTypeByName($name)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenarten WHERE ka_komponentenart = ?;");
        $componentTypeName = 0;
        $query->bind_param("i", $componentTypeName);
        $componentTypeName = $name;
        $query->execute();
        if($query->error) {$query->close(); throw new \Exception("Selektieren der Komponentenarten fehlgeschlagen");}
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

        return $componentType;
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
        if($query->error) {$query->close(); throw new \Exception("Speichern der Komponentenart fehlgeschlagen");}
        $query->close();

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
        if($query->error) {$query->close(); throw new \Exception("Ändern der Komponentenarte fehlgeschlagen");}
        $query->close();
    }

    /**
     * Deletes a component type from the database and its existing attributes via id.
     * @param type $id
     * @throws \Exception
     */
    public static function deleteComponentTypeById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        // select 'wird_beschrieben_durch' rows with the id of Component Type
        $query = $connection->prepare("SELECT * FROM wird_beschrieben_durch WHERE komponentenarten_ka_id = ?");
        $componentTypeId = 0;
        $query->bind_param("i", $componentTypeId);
        $componentTypeId = $id;
        $query->execute();
        if($query->error) {$query->close(); throw new \Exception("Selektieren der Verknüpfungstabelle");}
        $result = $query->get_result();
        $query->close();
        // ---

        while($row = $result->fetch_assoc())
        {
            // delete component attributes
            $query = $connection->prepare("DELETE FROM komponentenattribute WHERE kat_id = ?");
            $attributeId = 0;
            $query->bind_param("i", $attributeId);
            $attributeId = $row["komponentenattribute_kat_id"];
            $query->execute();
            if($query->error) {$query->close(); throw new \Exception("Selektieren der Attribute fehlgeshlagen");}
            $query->close();
            // ---
        }

        // delete linking table rows between 'komponentenarten' and 'Komponentenattribute'
        $query = $connection->prepare("DELETE FROM wird_beschrieben_durch WHERE komponentenarten_ka_id = ?");
        $componentTypeId = 0;
        $query->bind_param("i", $componentTypeId);
        $componentTypeId = $id;
        $query->execute();
        if($query->error) {$query->close(); throw new \Exception("Löschen der Verknüpfungstabelle fehlgeschlagen");}
        $query->close();
        // ---

        // delete Component Types
        $query = $connection->prepare("DELETE FROM komponentenarten WHERE ka_id = ?;");
        $componentTypeId = 0;
        $query->bind_param("i", $componentTypeId);
        $componentTypeId = $id;
        $query->execute();
        if($query->error) {$query->close(); throw new \Exception("Löschen der Komponentenarten fehlgeschlagen");}
        $query->close();
        // ---
    }

    /**
     * returns true if no Component references to this Component Type
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public static function canComponentTypeBeDeleted($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenarten INNER JOIN komponenten ON komponentenarten.ka_id = komponenten.k_id WHERE komponentenarten.ka_id = ?;");
        $componentTypeId = 0;
        $query->bind_param("i", $componentTypeId);
        $componentTypeId = $id;
        $query->execute();
        if($query->error) {$query->close(); throw new \Exception("Selektieren der Komponentenarten fehlgeschlagen");}
        $result = $query->get_result();
        $query->close();

        if($row = $result->fetch_assoc())
        {
            return false;
        }

        return true;
    }
}