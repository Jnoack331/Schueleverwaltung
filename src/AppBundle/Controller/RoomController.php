<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\RoomRepository;
use AppBundle\Entity\Room;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends Controller
{
    /**
     * Fetches all Rooms from the database and renders a template to display them
     *
     * @Route("/room", name="room_index")
     */
    public function indexAction(Request $req) {
        try {
            $rooms = RoomRepository::getAllRooms();
        } catch (Exception $e) {
            return $this->render("room_view", [
               "message" => "Fehler beim Laden der Räume"
            ]);
        }
        return $this->render("room_view", ["rooms" => $rooms]);
    }

    /**
     * Creates a new Room object from the data passed in $req
     * Saves the newly created object into the database
     *
     * @Route("/room/create", name="room_create")
     */
    public function createAction(Request $req) {
        if ($req->getMethod() === "GET") {
            return $this->render("room_create", []);
        } else {
            $room = new Room();
            $room->setNumber($req->get("number"));
            $room->setDescription($req->get("description"));
            $room->setNote($req->get("note"));

            try {
                $id = RoomRepository::createRoom($room);
            } catch (Exception $e) {
                return $this->render("room_create", [
                    "message" => "Fehler beim Erstellen des Raums"
                ]);
            }

            return $this->redirectToRoute("room_detail", ["id" => $id]);
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
            return $this->render("room_detail", [
                "message" => "Fehler beim Laden des Raums"
            ]);
        }

        if ($req->getMethod() === "GET") {
            // Show the room with $id
            return $this->render("room_detail", [
                "number"      => $room->getNumber(),
                "description" => $room->getDescription(),
                "note"        => $room->getNote()
            ]);
        } else {
            // Edit the room with $id
            $room->setNumber($req->get("number"));
            $room->setDescription($req->get("description"));
            $room->setNote($req->get("note"));

            try {
                $id = RoomRepository::updateRoom($room);
            } catch (Exception $e) {
                return $this->render("room_detail", [
                   "message" => "Fehler beim Speichern der Änderungen"
                ]);
            }
        }
    }

    /**
     * @Route("/room/delete/{id}", name="room_delete", requirements={"id": "\d+"})
     */
    public function deleteAction($id, Request $req) {
        try {
            RoomRepository::deleteRoomById($id);
        } catch (Exception $e) {
            return $this->render("room_delete", [
                "message" => "Fehler beim Löschen des Raums"
            ]);
        }

        $this->redirectToRoute("room_index", []);
    }
}
