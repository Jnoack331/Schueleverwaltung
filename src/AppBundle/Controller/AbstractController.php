<?php
/**
 * Created by PhpStorm.
 * User: obi
 * Date: 26.07.17
 * Time: 10:10
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractController extends Controller {
    protected function renderError($tpl, \Exception $e, $id = null) {
        if ($id !== null) {
            return $this->render($tpl, [
                "message" => $e->getMessage(),
                "id" => $id
            ]);
        } else {
            return $this->render($tpl, ["message" => $e->getMessage()]);
        }
    }
}