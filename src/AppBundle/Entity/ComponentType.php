<?php

namespace AppBundle\Entity;

class ComponentType extends AbstractModel
{
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
     * @return array
     * @throws \Exception
     */
    public function getAttributes()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT attribut.* FROM komponentenattribute AS attribut INNER JOIN wird_beschrieben_durch as wbd on attribut.kat_id = wbd.komponentenattribute_kat_id INNER JOIN komponentenarten ON komponentenarten.ka_id = wbd.komponentenarten_ka_id WHERE komponentenarten.ka_id = ?;");
        $query->bind_param("i", $this->getId());
        $query->execute();

        $result = $connection->query($query);

        $attributes = [];

        while($row = $result->fetch_assoc())
        {
            $attribute = new Attribute();
            $attribute->setId($row["kat_id"]);
            $attribute->setName($row["kat_bezeichnung"]);

            $attributes[] = $attribute;
        }

        if($connection->error)
        {
            throw new \Exception("Selektieren der Attribute fehlgeschlagen");
        }

        return $attributes;
    }
}
