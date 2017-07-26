<?php
/**
 * A software component. 
 */
namespace AppBundle\Entity;


use AppBundle\Entity\Repository\ComponentTypeRepository;
use AppBundle\Entity\Repository\RoomRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

class Component extends AbstractEntity implements ValidatingEntity
{
    private $roomId;
    private $supplierId;
    private $purchaseDate;
    private $warrantyDuration;
    private $note;
    private $producer;
    private $componentTypeId;
    private $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

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

    /**
     * @return Room
     */
    public function getRoom()
    {
        return RoomRepository::getRoomById($this->getRoomId());
    }

    /**
     * @return ComponentType|null
     */
    /*
    public function getComponentType(){
        return ComponentTypeRepository::getComponentTypeById($this->getComponentTypeId());
    }
    */

    /**
     * Obtains the supplier
     * of this software component by accessing the database.
     * @return Supplier
     * @throws \Exception
     */
    public function getSupplier()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM lieferant WHERE l_id = ?;");

        $supplierId = 0;

        $query->bind_param("i", $supplierId);

        $supplierId = $this->getSupplierId();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren des Lieferants fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();
        $row = $result->fetch_assoc();

        //Data -> Supplier Kann ausgelagert werden in ein 
        //DataAccessObject/Transformer o.ä.
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

    //TODO: checken ob das nicht doch noch gebraucht wird

    public function getComponentType()
    {
        return ComponentTypeRepository::getComponentTypeById($this->getComponentTypeId());
        /*
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponentenarten WHERE ka_id = ?;");

        $componentTypeId = 0;

        $query->bind_param("i", $componentTypeId);

        $componentTypeId = $this->getComponentTypeId();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren der Komponentenart fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();
        $row = $result->fetch_assoc();

        if($row == NULL)
        {
            return NULL;
        }

        $componentType = new ComponentType();
        $componentType->setId($row["ka_id"]);
        $componentType->setType($row["ka_komponentenart"]);

        return $componentType;
        */
    }


    /**
     * @return array
     */
    public function getComponentAttributeValues()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM komponente_hat_attribute WHERE komponenten_k_id = ?;");

        $id = 0;

        $query->bind_param("i", $id);

        $id = $this->getId();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren der Attributwerte fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();

        $attributeValues = [];

        while($row = $result->fetch_assoc())
        {
            $attributeValue = new AttributeValue();
            $attributeValue->setId($row["komponenten_k_id"]);
            $attributeValue->setAttributeId($row["komponentenattribute_kat_id"]);
            $attributeValue->setValue($row["khkat_wert"]);

            $attributeValues[] = $attributeValue;
        }

        return $attributeValues;
    }

    public function validate() {

        if($this->getName() == null || $this->getName() == ""){
            throw new Exception("Bitte geben Sie einen Namen an");
        }
        if($this->getRoom() === null){
            throw new Exception("Bitte geben Sie einen gültigen Raum an");
        }
        if($this->getPurchaseDate() === null){
            throw new Exception("Bitte geben Sie ein gültiges Datum an");
        }
        if($this->GetComponentType() === null){
            throw new Exception("Bitte geben Sie eine gültige Komponentenart an");
        }
    }
}
