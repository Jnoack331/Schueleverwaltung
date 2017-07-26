<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 08:55
 */

namespace AppBundle\Entity;

class AttributeValue extends AbstractEntity
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

    /**
     * @return Attribute
     */
    public function getAttribute()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("Select * from komponentenattribute where kat_id = ?;");

        $attributeId = 0;

        $query->bind_param("i", $attributeId);

        $this->getAttributeId();

        $query->execute();

        $result = $query->get_result();
        $query->close();
        $row = $result->fetch_assoc();

        $attribute = new Attribute();
        $attribute->setId($row["kat_id"]);
        $attribute->setName($row["kat_bezeichnung"]);

        return $attribute;
    }
}