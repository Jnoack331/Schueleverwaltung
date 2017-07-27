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

use Symfony\Component\Config\Definition\Exception\Exception;

class Attribute extends AbstractEntity implements ValidatingEntity {
    private $name;

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function validate() {
        if ($this->name === null || $this->name === "") {
            throw new Exception("Bitte geben Sie einen Attributnamen ein");
        }
    }
}