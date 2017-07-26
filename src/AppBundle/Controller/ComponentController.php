<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\Repository\ComponentRepository;
use AppBundle\Entity\Repository\ComponentTypeRepository;
use AppBundle\Entity\Repository\RoomRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ComponentController extends Controller
{
    /**
     * Fetches all Components from the database
     * Renders the corresponding template to display the Components
     *
     * @Route("/component", name="component_index")
     */
    public function indexAction(Request $req, SessionInterface $session)
    {
        $message = $session->get('message');
        $session->remove('message');
        //get all components
        $components = ComponentRepository::getAllComponents();
        return $this->render("component/index.html.twig", array(
            "components" => $components,
            "message" => $message
        ));
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
                $component->setName($name);
            }
            //set room id
            $room_id = $req->get("room_id");
            if($room_id === null || $room_id === ""){
                $error = "Bitte geben sie einen gültigen Raum an";
            }else{
                //check if room exists
                try{
                    $room = RoomRepository::getRoomById($room_id);
                    if(!$room){
                        $error = "Der Raum existiert nicht";
                    }else{
                        $component->setRoomId($room_id);
                    }
                }catch (Exception $exception){
                    $error = "Es gab einen Fehler beim abfragen des Raumes";
                }
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
            $component->setSupplierId(0);
            //component type
            $type_id = $req->get("type_id");
            //check if component type with this id exists
            $type = null;
            try{
                $type = ComponentTypeRepository::getComponentTypeById($type_id);
            }catch(Exception $ex){
                $error = "Es gab einen Fehlern beim Laden der Komponententypen";
            }
            if(!$type){
                $error = "Komponententyp existiert nicht";
            }else{
                $component->setComponentTypeId($type_id);
            }
            if(!$error){
                //create component object
                try{
                    $id = ComponentRepository::createComponent($component);
                    $component->setId($id);
                    //redirect to edit if everything was successful
                    return $this->redirectToRoute("component_edit", array("id" => $component->getId()));
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
            $rooms = array();
            $types = array();
            $error = false;
            try{
                //get rooms
                $rooms = RoomRepository::getAllRooms();
                //get component types
                $types = ComponentTypeRepository::getAllComponentTypes();

            }catch(Exception $exception){
                $error = "Es gab einen Fehler beim Datenbankzugriff";
            }
            return $this->render("component/create.html.twig", array(
                "rooms" => $rooms,
                "types" => $types,
                "messsage" => $error,       //TODO: meldungen verbessern
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
    public function editAction($id, Request $req, SessionInterface $session)
    {
        //get error message from session
        $message = $session->get('message');
        $session->remove('message');
        //get component by id
        $component = ComponentRepository::getComponentById($id);
        //get rooms
        $rooms = RoomRepository::getAllRooms();
        //get types
        $types = ComponentTypeRepository::getAllComponentTypes();
        return $this->render("component/edit.html.twig", array(
            "component" => $component,
            "rooms" => $rooms,
            "types" => $types,
            "message" => $message
        ));
    }

    /**
     * @Route("/component/{id}/delete", name="component_delete", requirements={"id": "\d+"})
     */
    public function deleteAction($id, Request $request, SessionInterface $session){
        //check if method is post

        //get component by id
        $component = ComponentRepository::getComponentById($id);
        if(!$component){
            return $this->createNotFoundException("Komponenten wurde nicht gefunden");
        }else{
            //check if component can be deleted
            //TODO: this
            if(true){
                ComponentRepository::deleteComponentById($component->getId());
                $session->set('message', 'Komponente wurde gelöscht');
                return $this->redirectToRoute('component_index');
            }else{
                //TODO: get error why component can't be created
                $error = "Grund hier";
                $session->set('message', "Komponente konnte nicht gelöscht werden: $error");
                return $this->redirect($request->headers->get('referer'));
            }
        }

    }
}