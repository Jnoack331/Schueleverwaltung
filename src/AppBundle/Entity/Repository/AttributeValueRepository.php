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
        return new AttributeValue();
    }

    public static function getAttributeValuesByComponentId($componentId){

    }

    public static function createAttributeValue($attributeValue){

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