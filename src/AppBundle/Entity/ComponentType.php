<?php
/**
 * Defines a type of software component.
 */
namespace AppBundle\Entity;

use AppBundle\Entity\Repository\ComponentTypeRepository;
use AppBundle\Entity\ValidatingEntity;
use Symfony\Component\Config\Definition\Exception\Exception;

class ComponentType extends AbstractEntity implements ValidatingEntity {
    private $type;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Obtains all attributes available for this component type
     * by accessing the database.
     * @return array
     * @throws \Exception
     */
    public function getAttributes()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT attribut.* FROM komponentenattribute AS attribut INNER JOIN wird_beschrieben_durch as wbd on attribut.kat_id = wbd.komponentenattribute_kat_id INNER JOIN komponentenarten ON komponentenarten.ka_id = wbd.komponentenarten_ka_id WHERE komponentenarten.ka_id = ?;");

        $id = 0;

        $query->bind_param("i", $id);

        $id = $this->getId();

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
     * @return null|string
     * @throws \Exception
     */
    public function getAttributeNames()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT attribut.* FROM komponentenattribute AS attribut INNER JOIN wird_beschrieben_durch as wbd on attribut.kat_id = wbd.komponentenattribute_kat_id INNER JOIN komponentenarten ON komponentenarten.ka_id = wbd.komponentenarten_ka_id WHERE komponentenarten.ka_id = ?;");

        $id = 0;

        $query->bind_param("i", $id);

        $id = $this->getId();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren der Attribute fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();

        $firstRow = $result->fetch_assoc();

        if($firstRow == NULL)
        {
            return NULL;
        }

        $attributeNames = $firstRow["ka_komponentenart"];

        while($row = $result->fetch_assoc())
        {
            $attributeNames .= "," . $row["ka_komponentenart"];
        }

        return $attributeNames;
    }

    public function validate() {
        if ($this->type === null || $this->type === "") {
            throw new Exception("Bitte geben Sie einen Namen für die Komponentenkategorie ein");
        }

        if (ComponentTypeRepository::getComponentTypeByName($this->type) !== null) {
            throw new Exception("Diese Komponentenkategorie existiert bereits");
        }
    }
}
