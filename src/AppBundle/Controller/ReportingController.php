<?php

namespace AppBundle\Controller;

/**
 * Handles logic for users and their roles.
 */

use AppBundle\Entity\Repository\ComponentTypeRepository;
use AppBundle\Entity\Repository\RoomRepository;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class ReportingController extends Controller
{
    /**
     * @Route("/", name="reporting_index")
     */
    public function indexAction(Request $request)
    {
        try{
            $filter = $request->get("q");
            if($filter !== null && $filter != "all"){
                //search for rooms with component type
                $rooms = RoomRepository::getRoomsByTypeID($filter);
            }else{
                //get all rooms
                $rooms = RoomRepository::getAllRooms();
            }
            //get types
            $types = ComponentTypeRepository::getAllComponentTypes();
            //render template with users
            return $this->render('reporting/index.html.twig', [
                'rooms' => $rooms,
                'types' => $types,
                'filter' => $filter
            ]);
        }catch (Exception $exception){

        }

    }
    /**
     * @Route("/reporting/{id}", name="reporting_room", requirements={"id": "\d+"})
     */
    public function roomAction(Request $request, $id)
    {
        //get room
        try{
            $room = RoomRepository::getRoomById($id);
            if(!$room){
                return $this->createNotFoundException("Room not found");
            }
            $components = $room->getComponents();
            //render template with users
            return $this->render('reporting/room.html.twig', [
                'room' => $room,
                'components' => $components
            ]);
        }catch (Exception $ex){
            //TODO: THIS
        }

    }
}
