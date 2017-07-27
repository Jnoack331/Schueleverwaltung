<?php

namespace AppBundle\Controller;

/**
 * Controller for ComponentType View.
 */
use AppBundle\AppBundle;
use AppBundle\Entity\ComponentType;
use AppBundle\Entity\Repository\AttributeRepository;
use AppBundle\Entity\Repository\ComponentTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\VarDumper\VarDumper;
use \AppBundle\Entity\Attribute;

class ComponentTypeController extends AbstractController {
    /**
     * Fetches all ComponentKinds from the database and renders a template to display them
     *
     * @Route("/component_kind", name="component_kind_index")
     */
    public function indexAction(Request $req,SessionInterface $session) {
        try {
            $componentTypes = ComponentTypeRepository::getAllComponentTypes();
        } catch (Exception $e) {
            return $this->renderError("componentType/list.html.twig", $e);
        }

        $message = $session->get('message');
        $session->remove('message');
        return $this->render("componentType/list.html.twig", [
            "componenttypes" => $componentTypes,
            "message"   => $message
        ]);
    }

    /**
     * Fetches the object identified by $id from the database
     * Fetches the object identified by $id from the database
     * Edits the object with the data passed in $req
     * Saves the edited object back into the database
     *
     * @Route("/component_kind/{id}", name="component_kind_edit", requirements={"id": "\d+"})
     */
    public function editAction($id, Request $req) {
        try {
            $componentType = ComponentTypeRepository::getComponentTypeById($id);
        } catch (Exception $e) {
            return $this->renderError("componentType/list.html.twig", $e);
        }

        if ($req->getMethod() === "GET") {

            return $this->render("componentType/detail.html.twig", [
                "componenttype" => $componentType,
                "attributes" => $componentType->getAttributes(),
                "id" => $id
            ]);
        } else {
            $componentType->setType($req->get("type"));
            $attributes = $req->get("attributevalues");
            foreach ($attributes as $attribute) {
                if (!isset($attribute['id'])) {
                    $newAttribute = new Attribute();
                    $newAttribute->setName($attribute['name']);
                    AttributeRepository::createAttribute($id, $newAttribute);
                } elseif (isset($attribute['id']) && empty($attribute['name'])) {
                    AttributeRepository::deleteAttributeById($attribute['id']);
                } elseif (isset($attribute['id']) && !empty($attribute['name'])) {
                    $oldattribute = AttributeRepository::getAttributeById($attribute['id']);
                    $oldattribute->setName($attribute['name']);
                    AttributeRepository::updateAttribute($oldattribute);
                }
            }
            try {
                ComponentTypeRepository::updateComponentType($componentType);
            } catch (Exception $e) {
                return $this->renderError("componentType/detail.html.twig", $e, $id);
            }

            return $this->redirectToRoute("component_kind_index", [
                "message" => "Komponentenkategorie wurde erfolgreich bearbeitet"
            ]);
        }
    }

    /**
     * @Route("/component_kind/attribute/delete/{id}", name="component_kind_delete_attr", requirements={"id": "\d+"})
     */
    public function deleteAttributeAction($id, Request $req) {
        try {
            if (ComponentTypeRepository::canComponentTypeBeDeleted($id)) {
                ComponentTypeRepository::deleteComponentTypeById($id);
            } else {
                return $this->render("componentType/list.html.twig", [
                    'message' => 'konnte nicht l&ouml;schen'
                ]);
            }
        } catch (Exception $e) {
            return $this->renderError("componentType/detail.html.twig", $e);
        }

        return $this->redirectToRoute("component_kind_edit");
    }

    /**
     * Fetches the object identified by $id from the database
     * Edits the object with the data passed in $req
     * Saves the edited object back into the database
     *
     * @Route("/component_kind/create", name="component_kind_create")
     */
    public function createAction(Request $req) {
        if ($req->getMethod() === "GET") {
            return $this->render('componentType/create.html.twig');
        } else {
            $componentType = new ComponentType();
            $componentType->setType($req->get("kind"));

            try {
                $componentType->validate();
                $id = ComponentTypeRepository::createComponentType($componentType);
            } catch (Exception $e) {
                return $this->renderError("componentType/create.html.twig", $e);
            }

            return $this->redirectToRoute('component_kind_edit', ['id' => $id]);
        }
    }

    /**
     * @Route("/component_kind/delete/{id}", name="component_kind_delete")
     */
    public function deleteAction($id, Request $req,SessionInterface $session) {
        try {
            ComponentTypeRepository::canComponentTypeBeDeleted($id);
            ComponentTypeRepository::deleteComponentTypeById($id);
        } catch (\Exception $e) {
            $session->set('message', 'Konnte Komponententyp nicht Löschen da sie noch mit Komponenten verknüpft ist.');
            return $this->redirectToRoute("component_kind_index");
        }

        return $this->redirectToRoute("component_kind_index");
    }
}
