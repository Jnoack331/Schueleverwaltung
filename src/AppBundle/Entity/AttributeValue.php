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
    private $attributeId;
    private $value;

    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
    public function getValue()
    {
        return $this->value;
    }

    public function getAttribute()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("Select * from komponentenattribute where kat_id = ?;");
        $query->bind_param("i", $this->getAttributeId());
        $query->execute();

        $result = $connection->query($query);
        $row = $result->fetch_row();

        $attribute = new Attribute();
        $attribute->setId($row["kat_id"]);
        $attribute->setName($row["kat_bezeichnung"]);

        return $attribute;
    }
}