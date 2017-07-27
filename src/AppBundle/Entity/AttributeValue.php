<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 08:55
 * 
 * Specifies a value for a certain Attribute,
 * e.g. "1 GB" for an attribute "RAM/Memory"
 * 
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Repository\ComponentRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

class AttributeValue extends AbstractEntity implements ValidatingEntity
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
     * @return Attribute|null
     * @throws \Exception
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

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren des Attributs fehlgeschlagen");
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

    public function getComponent(){
        return ComponentRepository::getComponentById($this->getId());
    }

    public function validate()
    {
        //check if component exists
        if(!$this->getComponent()){
            throw new Exception("Bitte geben Sie eine gültige Komponente an");
        }
        //check if attribute exists
        if(!$this->getAttribute()){
            throw new Exception("Bitte geben Sie ein gültiges Attribute an");
        }
    }
}