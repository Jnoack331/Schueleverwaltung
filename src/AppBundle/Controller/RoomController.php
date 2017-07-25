<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\RoomRepository;
use AppBundle\Entity\Room;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends Controller
{
    /**
     * Fetches all Rooms from the database and renders a template to display them
     *
     * @Route("/room", name="room_index")
     */
    public function indexAction(Request $req) {
        $rooms = RoomRepository::getAllRooms();
        $this->render("room_view", ["rooms" => $rooms]);
    }

    /**
     * Creates a new Room object from the data passed in $req
     * Saves the newly created object into the database
     *
     * @Route("/room/create", name="room_create")
     */
    public function createAction(Request $req) {
        $room = new Room();
        $room->setNumber($req->get("number"));
        $room->setDescription($req->get("description"));
        $room->setNote($req->get("note"));

        RoomRepository::createRoom($room);
    }

    /**
     * Fetches the Room object identified by id from the database
     * Edits the object with the data passe in $req
     * Saves the edited object back to the database
     *
     * @Route("/room/{id}", name="room_detail" requirements={"id": "\d+"})
     */
    public function detailAction($id, Request $req) {
        $room = RoomRepository::getRoomById($id);

        if ($req->getMethod() === "GET") {
            // Show the room with $id
            $this->render("room_detail_view", [
                "number"      => $room->getNumber(),
                "description" => $room->getDescription(),
                "note"        => $room->getNote()
            ]);
        } else {
            // Edit the room with $id
            $room->setNumber($req->get("number"));
            $room->setDescription($req->get("description"));
            $room->setNote($req->get("note"));

            RoomRepository::updateRoom($room);
        }
    }
}
