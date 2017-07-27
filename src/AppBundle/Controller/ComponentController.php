<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeValue;
use AppBundle\Entity\Component;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\Repository\AttributeRepository;
use AppBundle\Entity\Repository\AttributeValueRepository;
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
                $component->setId($id);
                //get attributes
                $attributes = $component->getAttributes();
                /** @var Attribute $attribute */
                foreach ($attributes as $attribute) {
                    //create attribute values
                    $attribute_value = new AttributeValue();
                    $attribute_value->setId($component->getId());
                    $attribute_value->setAttributeId($attribute->getId());
                    $attribute_value->setValue("");
                    AttributeValueRepository::createAttributeValue($attribute_value);
                }
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
            //TODO: check if new ComponentTypeId is different from old one
            $newComponentTypeId = $req->get("type_id");
            if($newComponentTypeId != $component->getComponentTypeId()){
                //if component type changes -> delete all previous values
                try{
                    //delete old values (with old component type id)
                    $component->deleteAttributeValues();
                    //set new component id
                    $component->setComponentTypeId($newComponentTypeId);
                    //TODO: create new values without content
                    $attributes_new = $component->getAttributes();
                    /** @var Attribute $attribute_new */
                    foreach ($attributes_new as $attribute_new){
                        $attribute_value = new AttributeValue();
                        $attribute_value->setId($component->getId());
                        $attribute_value->setAttributeId($attribute_new->getId());
                        $attribute_value->setValue("");
                        AttributeValueRepository::createAttributeValue($attribute_value);
                    }
                    $attributes = $attributes_new;
                }catch (Exception $exception){
                    //TODO: Error
                }
            //if component type stay the same, save attribute values
            }else{
                //get attribute values from form
                $attribute_value_parameters = $req->get("attribute-value");   //array(attribute_id => value, ...);
                if($attribute_value_parameters == null){
                    $attribute_value_parameters = array();
                }
                foreach ($attribute_value_parameters as $attribute_id => $value){
                    //get existing attribute values
                    try{
                        $attribute_value = AttributeValueRepository::getAttributeValue($component->getId(), $attribute_id);
                        $attribute_value->setValue($value);
                        //no need to validate, since ids should be right and value can be empty
                        //save attribute value to db
                        AttributeValueRepository::updateAttributeValue($attribute_value);
                    }catch (Exception $exception){
                        $message = $exception->getMessage();
                        //TODO: Error
                    }
                }
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