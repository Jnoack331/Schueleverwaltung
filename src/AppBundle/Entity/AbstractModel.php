<?php

/**
 * Defines an entity in the database, accessible
 * via ID as primary key.
 */

namespace AppBundle\Entity;

class AbstractModel
{
    private $id;

    /**
     * @return an id, normally a primary key
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
