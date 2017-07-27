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
use AppBundle\Entity\Component;
use AppBundle\Entity\ManagedConnection;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\VarDumper\VarDumper;

class AttributeRepository
{
    /**
     * @param $id
     * @return Attribute|null
     * @throws \Exception
     */
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

    /**
     * @param $typeId
     * @return array
     * @throws \Exception
     */
    public static function getAttributesByComponentTypeId($typeId){
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query_string = "SELECT kat.kat_id, kat.kat_bezeichnung FROM komponentenarten as ka ";
        $query_string .= "INNER JOIN wird_beschrieben_durch b ON ka.ka_id = b.komponentenarten_ka_id ";
        $query_string .= "INNER JOIN komponentenattribute kat ON kat.kat_id = b.komponentenattribute_kat_id ";
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
            $attribute->setId($row["kat_id"]);
            $attribute->setName($row["kat_bezeichnung"]);

            $attributes[] = $attribute;
        }

        return $attributes;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
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

    /**
     * @param $componentTypeId
     * @param Attribute $attribute
     * @return int|string
     */
    public static function createAttribute($componentTypeId, Attribute $attribute) {
        $errorMessage = "Fehler beim Einfügen des Attributs";

        $managedConn = new ManagedConnection();
        $conn = $managedConn->getConnection();

        $query = $conn->prepare("INSERT INTO komponentenattribute (kat_bezeichnung) VALUES (?);");

        $attributeName = "";
        $query->bind_param("s", $attributeName);
        $attributeName = $attribute->getName();

        $query->execute();
        if ($query->error) {
            $query->close();
            throw new Exception($errorMessage);
        }
        $query->close();

        $insertId = mysqli_insert_id($conn);

        $query = $conn->prepare("INSERT INTO wird_beschrieben_durch (komponentenarten_ka_id, komponentenattribute_kat_id) VALUES  (?, ?);");

        $typeId = 0;
        $attributeId = 0;
        $query->bind_param("ii", $typeId, $attributeId);
        $typeId = $componentTypeId;
        $attributeId = $insertId;

        $query->execute();
        if ($query->error) {
            $query->close();
            throw new Exception($errorMessage);
        }
        $query->close();

        return $insertId;
    }

    /**
     * @param Attribute $attribute
     */
    public static function updateAttribute(Attribute $attribute)
    {
        $errorMessage = "Fehler beim Einfügen des Attributs";

        $managedConn = new ManagedConnection();
        $conn = $managedConn->getConnection();

        $query = $conn->prepare("UPDATE komponentenattribute SET kat_bezeichnung = ? WHERE kat_id = ?;");

        $attributeName = "";
        $attributeId = 0;
        $query->bind_param("si", $attributeName, $attributeId);
        $attributeId = $attribute->getId();
        $attributeName = $attribute->getName();

        $query->execute();
        if ($query->error) {
            $query->close();
            throw new Exception($errorMessage);
        }
        $query->close();
    }

    /**
     * @param $id
     */
    public static function deleteAttributeById($id) {
        $managedConn = new ManagedConnection();
        $conn = $managedConn->getConnection();

        $query = $conn->prepare("DELETE FROM komponente_hat_attribute WHERE komponentenattribute_kat_id = ?;");

        $attributeId = 0;
        $query->bind_param("i", $attributeId);

        $attributeId = $id;

        $query->execute();

        $query = $conn->prepare("DELETE FROM wird_beschrieben_durch WHERE komponentenattribute_kat_id = ?;");

        $attributeId = 0;
        $query->bind_param("i", $attributeId);

        $attributeId = $id;

        $query->execute();

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