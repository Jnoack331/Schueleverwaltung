<?php

namespace AppBundle\Controller;

/**
 * Controller for ComponentType View.
 */
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\Repository\ComponentTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class ComponentTypeController extends AbstractController {
    /**
     * Fetches all ComponentKinds from the database and renders a template to display them
     *
     * @Route("/component_kind", name="component_kind_index")
     */
    public function indexAction(Request $req) {
        try {
            $componentTypes = ComponentTypeRepository::getAllComponentTypes();
        } catch (Exception $e) {
            return $this->renderError("component_kind_index", $e);
        }

        return $this->render("component_kind_index", ["types" => $componentTypes]);
    }

    /**
     * Fetches the object identified by $id from the database
     * Edits the object with the data passed in $req
     * Saves the edited object back into the database
     *
     * @Route("/component_kind/{id}", name="component_kind_edit", requirements={"id": "\d+"})
     */
    public function editAction($id, Request $req) {
        try {
            $componentType = ComponentTypeRepository::getComponentTypeById($id);
            $attributes = $componentType->getAttributes();
        } catch (Exception $e) {
            return $this->renderError("component_kind_index", $e);
        }

        if ($req->getMethod() === "GET") {
            return $this->render("edit_component_types", [
                "type"       => $componentType->getType(),
                "attributes" => $attributes
            ]);
        } else {
            $componentType->setType($req->get("type"));

            try {
                $componentType->validate();
                ComponentTypeRepository::updateComponentType($componentType);
            } catch (Exception $e) {
                return $this->renderError("component_kind_edit", $e);
            }

            return $this->redirectToRoute("component_kind_index", []);
        }
    }

    /**
     * @Route("/component_kind/attribute/delete/{id}", name="component_kind_delete_attr", requirements={"id": "\d+"})
     */
    public function deleteAttributeAction($id, Request $req) {
        try {
            $attribute = ComponentTypeRepository::getAttributeById($id);
        } catch (Exception $e) {
            return $this->renderError("componentType/detail.html.twig", $e);
        }


    }

    /**
     * Fetches the object identified by $id from the database
     * Edits the object with the data passed in $req
     * Saves the edited object back into the database
     *
     * @Route("/component_kind/create", name="component_kind_create")
     */
    public function createAction(Request $req) {
        $componentType = new ComponentType();
        $componentType->setType($req->get("kind"));

        try {
            $componentType->validate();
            ComponentTypeRepository::createComponentType($componentType);
        } catch (Exception $e) {
            return $this->renderError("component_kind_index", $e);
        }
    }
}
