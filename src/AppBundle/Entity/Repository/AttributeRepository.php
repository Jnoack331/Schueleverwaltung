<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 10:32
 *
 * Provides functions to modify component entities in
 * the database.
 *
 */

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\Attribute;
use AppBundle\Entity\ManagedConnection;

class AttributeRepository
{

    public static function getAttributeById($id){
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenattribute WHERE kat_id = ?;");

        $attributeId = 0;

        $query->bind_param("i", $attributeId);

        $attributeId = $id;

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Lesen des Attributes fehlgeschlagen");
        }

        $result = $query->get_result();

        $query->close();

        $row = $result->fetch_assoc();

        if($row == NULL)
        {
            return NULL;
        }

        $attribute = new Attribute();
        $attribute->setId($row["kat_id"]);
        $attribute->setName($row["kat_bezeichnung"]);

        return $attribute;
    }

    public static function getAttributesByComponentTypeId($typeId){
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query_string = "SELECT kat.kat_id, kat.kat_bezeichnung FROM komponentenarten as ka ";
        $query_string .= "LEFT JOIN wird_beschrieben_durch b ON ka.ka_id = b.komponentenarten_ka_id ";
        $query_string .= "LEFT JOIN komponentenattribute kat ON kat.kat_id = b.komponentenattribute_kat_id ";
        $query_string .= "WHERE ka.ka_id = ?";
        $query = $connection->prepare($query_string);

        $id = 0;

        $query->bind_param("i", $id);

        $id = $typeId;

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren der Attribute fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();

        $attributes = [];

        while($row = $result->fetch_assoc())
        {
            $attribute = new Attribute();
            if($row["kat_id"] == NULL){
                break;
            }
            $attribute->setId($row["kat_id"]);
            if($row["kat_bezeichnung"] == NULL){
                break;
            }
            $attribute->setName($row["kat_bezeichnung"]);

            $attributes[] = $attribute;
        }

        return $attributes;
    }

    public static function canAttributeBeDeleted($id) {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenattribute INNER JOIN komponente_hat_attribute ON komponentenattribute.kat_id = komponente_hat_attribute.komponentenattribute_kat_id WHERE komponentenattribute.kat_id = ?;");

        $attributeId = 0;

        $query->bind_param("i", $attributeId);

        $attributeId = $id;

        $query->execute();

        if ($query->error) {
            $query->close();
            throw new \Exception("Selektieren des Raumes fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();

        if ($row = $result->fetch_assoc()) {
            return false;
        }

        return true;
    }

    public static function deleteAttributeById($id) {
        $managedConn = new ManagedConnection();
        $conn = $managedConn->getConnection();

        $query = $conn->prepare("DELETE FROM komponentenattribute WHERE kat_id = ?;");

        $attributeId = 0;
        $query->bind_param("i", $attributeId);

        $attributeId = $id;

        $query->execute();

        if($query->error) {
            $query->close();
            throw new Exception("Löschen des Attributs fehlgeschlagen");
        }
    }
}