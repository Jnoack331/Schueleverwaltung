<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 08:55
 */

namespace AppBundle\Entity;

class AttributeValue extends AbstractModel
{
    private $attributeID;
    private $value;

    public function setAttributeID($attributeID)
    {
        $this->attributeID = $attributeID;
    }
    public function getAttributeID()
    {
        return $this->attributeID;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
    public function getValue()
    {
        return $this->value;
    }

    public function GetAttribute()
    {
        $Attribute = new Attribute();

        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("Select * from komponentenattribute where kat_id = ?;");
        $query->bind_param("i", $this->getAttributeID());
        $query->execute();

        $result = $connection->query($query);

        $Attribute->setId($result["kat_id"]);
        $Attribute->setName($result["kat_bezeichnung"]);

        return $Attribute;
    }
}