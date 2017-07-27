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


use AppBundle\Entity\AttributeValue;
use AppBundle\Entity\ManagedConnection;

class AttributeValueRepository
{

    /**
     * @param $component_id
     * @param $attribute_id
     * @return AttributeValue
     */
    public static function getAttributeValue($component_id, $attribute_id){
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponente_hat_attribute WHERE komponenten_k_id = ? AND komponentenattribute_kat_id = ?;");

        $componentId = 0;
        $attributeId = 0;

        $query->bind_param("ii", $componentId, $attributeId);


        $componentId = $component_id;
        $attributeId = $attribute_id;

        $query->execute();


        if($query->error)
        {
            $query->close();
            throw new \Exception("Lesen des Attributwerts fehlgeschlagen");
        }

        $result = $query->get_result();

        $query->close();

        $row = $result->fetch_assoc();

        if($row == NULL)
        {
            return NULL;
        }

        $attribute_value = new AttributeValue();
        $attribute_value->setId($row["komponenten_k_id"]);
        $attribute_value->setAttributeId($row["komponentenattribute_kat_id"]);
        $attribute_value->setValue($row["khkat_wert"]);
        return $attribute_value;
    }

    /**
     * @param AttributeValue $attributeValue
     * @return int|string
     * @throws \Exception
     */
    public static function createAttributeValue($attributeValue){
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("INSERT INTO komponente_hat_attribute(komponenten_k_id, komponentenattribute_kat_id, khkat_wert) VALUES (?, ?, ?);");

        $komp_id = 0;
        $kat_id = 0;
        $value = 0;

        $query->bind_param("iis", $komp_id, $kat_id, $value);

        $komp_id = $attributeValue->getId();
        $kat_id = $attributeValue->getAttributeId();
        $value = $attributeValue->getValue();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Erstellen des Wertes fehlgeschlagen");
        }

        $query->close();

        return mysqli_insert_id($connection);
    }

    /**
     * @param AttributeValue $attributeValue
     * @throws \Exception
     */
    public static function updateAttributeValue($attributeValue){
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("UPDATE komponente_hat_attribute SET khkat_wert = ? WHERE komponenten_k_id = ? AND komponentenattribute_kat_id = ?;");

        $value = "";
        $komp_id = 0;
        $kat_id = 0;

        $query->bind_param("sii", $value, $komp_id, $kat_id);

        $value = $attributeValue->getValue();
        $komp_id = $attributeValue->getId();
        $kat_id = $attributeValue->getAttributeId();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Ändern des Attributwerts fehlgeschlagen");
        }

        $query->close();
    }

    /**
     * @param AttributeValue $attribute_value
     * @throws \Exception
     */
    public static function deleteAttributeValue($attributeValue){
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("DELETE FROM komponente_hat_attribute WHERE komponenten_k_id = ? AND komponentenattribute_kat_id = ?;");

        $k_id = 0;
        $kat_id = 0;

        $query->bind_param("ii", $k_id, $kat_id);

        $k_id = $attributeValue->getId();
        $kat_id = $attributeValue->getAttributeId();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Löschen des Attributes fehlgeschlagen");
        }

        $query->close();
    }

}