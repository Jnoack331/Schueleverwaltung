<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 08:54
 * 
 * Defines an attribute for a software
 * component, e.g. CPU, Resolution.
 * 
 */

namespace AppBundle\Entity;

class Attribute extends AbstractEntity
{
    private $name;

    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
}