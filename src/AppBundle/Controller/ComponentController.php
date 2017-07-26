<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component;
use AppBundle\Entity\Repository\ComponentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class ComponentController extends Controller
{
    /**
     * Fetches all Components from the database
     * Renders the corresponding template to display the Components
     *
     * @Route("/component", name="component_index")
     */
    public function indexAction(Request $req)
    {

    }

    /**
     * Get data for new component from previously displayed template
     * Create new component object from the data and insert it in the database
     *
     * @Route("/component/create", name="component_create")
     */
    public function createAction(Request $req)
    {
        //if method is POST -> try to create Component
        if($req->getMethod() === "POST"){
            $error = false;
            //create Component Object
            $component = new Component();
            //set name
            $name = $req->get("name");
            if($name === null || $name === ""){
                $error = "Bitte geben sie einen Namen für die Komponente an";
            }else{

            }
            //set room id
            $room_id = $req->get("room_id");
            if($room_id === null || $room_id === ""){
                $error = "Bitte geben sie einen gültigen Raum an";
            }else{
                $component->setRoomId($room_id);
            }
            //create date from string
            $date_string = $req->get("buy_date");
            $date = date_create_from_format("Y-m-d", $date_string);
            if(!$date){
                $error = "Ungültiges Datum";
            }else{
                $component->setPurchaseDate($date);
            }
            //set warranty duration
            $component->setWarrantyDuration($req->get("warranty"));
            //set note
            $component->setNote($req->get("note"));
            // set producer
            $component->setProducer($req->get("producer"));
            //TODO: set component types
            if(!$error){
                //create component object
                try{
                    //$id = ComponentRepository::createComponent($component);
                    //$component->setId($id);
                    // $this->redirectToRoute("component_edit", array("id" => $component->getId()));
                    $this->render("component/create.html.twig", array());
                }catch(Exception $exception){
                    return $this->render("component/create.html.twig",
                        array("message" => "Es gab einen Fehler beim erstellen der Componente"));
                }
            }else{
                return $this->render("component/create.html.twig", array(
                    "message" => $error,
                ));
            }




        }
        //if method is GET -> render template
        else{
            //get component types
            $types = array(
                0 => "PC",
                1 => "Drucker",
                2 => "Beamer",
            );
            return $this->render("component/create.html.twig", array(
                "types" => $types
            ));
        }
    }

    /**
     * Fetch existing object from the database by its id
     * Fill in the new data from the previously displayed template into the fetched object
     * Save the edited object to the database
     *
     * @Route("/component/{id}", name="component_edit", requirements={"id": "\d+"})
     */
    public function editAction($id, Request $req)
    {

    }
}