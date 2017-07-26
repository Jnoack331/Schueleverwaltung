<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\Repository\AttributeRepository;
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
        $rooms = array();
        $types = array();
        $error = false;
        try{
            //get rooms
            $rooms = RoomRepository::getAllRooms();
            //get component types
            $types = ComponentTypeRepository::getAllComponentTypes();
        }catch(Exception $exception){
            $error = "Es gab einen Fehler beim Zugriff auf die Datenbank";
            return $this->render("component/create.html.twig", array(
                "rooms" => $rooms,
                "types" => $types,
                "message" => $error,       //TODO: meldungen verbessern
            ));
        }
        //if method is POST -> try to create Component
        if($req->getMethod() === "POST"){
            //create Component Object
            $component = new Component();
            //set values and validate
            $component->setName($req->get("name"));
            $component->setRoomId($req->get("room_id"));
            $date_string = $req->get("buy_date");
            $date = date_create_from_format("Y-m-d", $date_string);
            $component->setPurchaseDate($date);
            $component->setWarrantyDuration($req->get("warranty"));
            $component->setNote($req->get("note"));
            $component->setProducer($req->get("producer"));
            $component->setSupplierId(0);
            $component->setComponentTypeId($req->get("type_id"));
            //validate component, show message if not valid
            try{
                $component->validate();
            }catch (Exception $exception){
                return $this->render("component/create.html.twig", array(
                        "rooms" => $rooms,
                        "types" => $types,
                        "message" => $exception->getMessage(),       //TODO: meldungen verbessern
                    )
                );
            }
            try{
                $id = ComponentRepository::createComponent($component);
                return $this->redirectToRoute("component_edit", array("id" => $id));
            }catch (Exception $exception){

                return $this->render("component/create.html.twig", array(
                        "rooms" => $rooms,
                        "types" => $types,
                        "message" => "Es gab einen Fehler beim Zugriff auf die Datenbank",       //TODO: meldungen verbessern
                    )
                );
            }

        }
        //if method is GET -> render template
        else{
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
        try{
            //get component by id
            $component = ComponentRepository::getComponentById($id);
            if(!$component){
                return $this->createNotFoundException("Komponente nicht gefunden");
            }
            //get rooms
            $rooms = RoomRepository::getAllRooms();
            //get types
            $types = ComponentTypeRepository::getAllComponentTypes();
            $attributes = AttributeRepository::getAttributesByComponentTypeId($component->getComponentTypeId());

        }catch (Exception $ex){
            //TODO: show error
            return $this->createNotFoundException("Server Fehler");
        }
        if($req->getMethod() === "GET"){

            return $this->render("component/edit.html.twig", array(
                "component" => $component,
                "attributes" => $attributes,
                "rooms" => $rooms,
                "types" => $types,
                "message" => $message
            ));
        }else{
            //create component and validate
            $component->setName($req->get("name"));
            $component->setRoomId($req->get("room_id"));
            $date_string = $req->get("buy_date");
            $date = date_create_from_format("Y-m-d", $date_string);
            $component->setPurchaseDate($date);
            $component->setWarrantyDuration($req->get("warranty"));
            $component->setNote($req->get("note"));
            $component->setProducer($req->get("producer"));
            $component->setComponentTypeId($req->get("type_id"));
            //set attribute values
            $attribute_values = $req->get("attribute-value");   //key = attribute id, value = new value
            foreach ($attribute_values as $attribute_id => $value){
                echo "$attribute_id - $value <br>";
            }
            try{
                $component->validate();
                ComponentRepository::updateComponent($component);
                $message = "Komponente erfolgreich bearbeitet";
            }catch (Exception $exception){
                $message = $exception->getMessage();
            }
            return $this->render("component/edit.html.twig", array(
                "component" => $component,
                "attributes" => $attributes,
                "rooms" => $rooms,
                "types" => $types,
                "message" => $message
            ));
        }

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