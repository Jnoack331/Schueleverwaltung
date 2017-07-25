<?php

namespace AppBundle\Entity;


class Component extends AbstractModel
{
    private $roomId;
    private $supplierId;
    private $purchaseDate;
    private $warrantyDuration;
    private $note;
    private $producer;
    private $componentTypeId;

    /**
     * @return mixed
     */
    public function getRoomId()
    {
        return $this->roomId;
    }

    /**
     * @param mixed $roomId
     */
    public function setRoomId($roomId)
    {
        $this->roomId = $roomId;
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @param mixed $supplierId
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
     * @return mixed
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * @param mixed $purchaseDate
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;
    }

    /**
     * @return mixed
     */
    public function getWarrantyDuration()
    {
        return $this->warrantyDuration;
    }

    /**
     * @param mixed $warrantyDuration
     */
    public function setWarrantyDuration($warrantyDuration)
    {
        $this->warrantyDuration = $warrantyDuration;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getProducer()
    {
        return $this->producer;
    }

    /**
     * @param mixed $producer
     */
    public function setProducer($producer)
    {
        $this->producer = $producer;
    }

    /**
     * @return mixed
     */
    public function getComponentTypeId()
    {
        return $this->componentTypeId;
    }

    /**
     * @param mixed $componentTypeId
     */
    public function setComponentTypeId($componentTypeId)
    {
        $this->componentTypeId = $componentTypeId;
    }

    public function GetSupplier()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM lieferant WHERE l_id = ?;");
        $query->bind_param("i", $this->getSupplierId());
        $query->execute();

        $result = $connection->query($query);
        $row = $result->fetch_row();

        $supplier = new Supplier();
        $supplier->setId($row["l_id"]);
        $supplier->setCompanyName($row["l_firmenname"]);
        $supplier->setStreet($row["l_strasse"]);
        $supplier->setZip($row["l_plz"]);
        $supplier->setCity($row["l_ort"]);
        $supplier->setPhone($row["l_tel"]);
        $supplier->setMobile($row["l_mobil"]);
        $supplier->setFax($row["l_fax"]);
        $supplier->setEmail($row["l_email"]);

        return $supplier;
    }

    public function GetComponentType()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenarten WHERE ka_id = ?;");
        $query->bind_param("i", $this->getComponentTypeId());
        $query->execute();

        $result = $connection->query($query);
        $row = $result->fetch_row();

        $componentType = new ComponentType();
        $componentType->setId($row["ka_id"]);
        $componentType->setType($row["ka_komponentenart"]);

        return $componentType;
    }

    /**
     *
     */
    public function GetComponentAttributeValues()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponente_hat_attribute WHERE komponenten_k_id = ?;");
        $query->bind_param("i", $this->getId());
        $query->execute();

        $result = $connection->query($query);

        $attributeValues = [];

        while($row = $result->fetch_assoc()) {
            $attributeValue = new AttributeValue();
            $attributeValue->setId($row["komponenten_k_id"]);
            $attributeValue->setAttributeId($row["komponentenattribute_kat_id"]);
            $attributeValue->setValue($row["khkat_wert"]);

            $attributeValues[] = $attributeValue;
        }

        return $attributeValues;
    }
}
