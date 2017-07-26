<?php
/**
 * Created by PhpStorm.
 * User: obi
 * Date: 26.07.17
 * Time: 11:09
 */

namespace AppBundle\Entity;

interface ValidatingEntity {
    public function validate();
}