<?php

namespace AppBundle\Controller;
/**
 * Controller for Room-View. 
 */
use AppBundle\Entity\Repository\RoomRepository;
use AppBundle\Entity\Room;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends AbstractController {
    /**
     * Fetches all Rooms from the database and renders a template to display them
     *
     * @Route("/room", name="room_list")
     */
    public function listAction(Request $req) {
        try {
            $rooms = RoomRepository::getAllRooms();
        } catch (Exception $e) {
            return $this->renderError("room_view", $e);
        }
        return $this->render("room/list.html.twig", ["rooms" => $rooms]);
    }

    /**
     * Creates a new Room object from the data passed in $req
     * Saves the newly created object into the database
     *
     * @Route("/room/create", name="room_create")
     */
    public function createAction(Request $req) {
        if ($req->getMethod() === "GET") {
            return $this->render("room/create.html.twig", []);
        } else {
            $room = new Room();
            $room->setNumber($req->get("number"));
            //var_dump($room->getNumber());
            $room->setDescription($req->get("description"));
            $room->setNote($req->get("note"));
            $room_with_name = RoomRepository::getRoomByNumber($room->getNumber());
            $message = "Raum wurde erfolgreich erstellt";
            try {
                if($room_with_name !== null){
                    throw new Exception("Ein Raum mit diesem Namen existiert bereits");
                }
                $room->validate();
            } catch (Exception $e) {
                $message = $e->getMessage();
                return $this->renderError("room/create.html.twig", $e);
            }
            try{
                $id = RoomRepository::createRoom($room);
            }catch (Exception $e){
                $message = "Es gab einen Fehler beim erstellen des Raums";
                return $this->renderError("room/create.html.twig", $e);
            }
            return $this->redirectToRoute("room_detail", array("id" => $id));
        }
    }

    /**
     * Fetches the Room object identified by id from the database
     * Edits the object with the data passe in $req
     * Saves the edited object back to the database
     *
     * @Route("/room/{id}", name="room_detail", requirements={"id": "\d+"})
     */
    public function detailAction($id, Request $req) {
        try {
            $room = RoomRepository::getRoomById($id);
        } catch (Exception $e) {
            return $this->renderError("room/detail.html.twig", $e, $id);
        }

        if ($req->getMethod() === "GET") {
            // Show the room with $id
            return $this->render("room/detail.html.twig", [
                "room"  => $room,
                "id" => $id
            ]);
        } else {
            // Edit the room with $id
            $room->setNumber($req->get("number"));
            $room->setDescription($req->get("description"));
            $room->setNote($req->get("note"));

            $message = "Raum wurde erfolgreich bearbeitet";
            try {
                $room->validate();
                try{
                    RoomRepository::updateRoom($room);
                }catch (Exception $exception){
                    $message = "Es gab einen Fehler beim erstellen des Raums";
                }
            } catch (Exception $e) {
                return $this->renderError("room/detail.html.twig", $e, $id);
            }
            $room = RoomRepository::getRoomById($id);

            return $this->render("room/detail.html.twig", [
                "room" => $room,
                "message" => $message
            ]);
        }
    }

    /**
     * @Route("/room/delete/{id}", name="room_delete", requirements={"id": "\d+"})
     */
    public function deleteAction($id, Request $req) {
        try {
            if (RoomRepository::canRoomBeDeleted($id)) {
                RoomRepository::deleteRoomById($id);
            } else {
                return $this->render("room/detail.html.twig", [
                    "message" => "Der Raum kann nicht gelöscht werden, da ihm Komponenten zugeordnet sind",
                    "id" => $id
                ]);
            }
        } catch (Exception $e) {
            return $this->renderError("room/list.html.twig", $e);
        }

        return $this->redirectToRoute("room_list", [
            "message" => "Raum wurde erfolgreich gelöscht"
        ]);
    }
}
