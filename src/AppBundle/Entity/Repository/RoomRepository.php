<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 09:21
 */

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\ManagedConnection;
use AppBundle\Entity\Room;

class RoomRepository
{
    /**
     * @return array
     * @throws \Exception
     */
    public static function getAllRooms()
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = "SELECT * FROM raeume;";
        $result = $connection->query($query);

        $rooms = [];

        while ($row = $result->fetch_assoc())
        {
            $room = new Room();
            $room->setId($row["r_id"]);
            $room->setNumber($row["r_nr"]);
            $room->setDescription($row["r_bezeichnung"]);
            $room->setNote($row["r_notiz"]);
            $rooms[] = $room;
        }

        if($connection->error)
        {
            throw new \Exception("Selektieren der Räume fehlgeschlagen");
        }

        return $rooms;
    }

    /**
     * @param $id
     * @return Room
     * @throws \Exception
     */
    public static function getRoomById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM raeume WHERE r_id = ?;");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $connection->query($query);

        $row = $result->fetch_row();

        $room = new Room();
        $room->setId($row["r_id"]);
        $room->setNumber($row["r_nr"]);
        $room->setDescription($row["r_bezeichnung"]);
        $room->setNote($row["r_notiz"]);

        if($connection->error)
        {
            throw new \Exception("Selektieren des Raumes fehlgeschlagen");
        }

        return $room;
    }

    /**
     * @param Room $room
     * @return int|string
     * @throws \Exception
     */
    public static function createRoom(Room $room)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("INSERT INTO raeume(r_nr, r_bezeichnung, r_notiz) VALUES (?, ?, ?);");
        $query->bind_param("sss", $room->getId(), $room->getNumber(), $room->getDescription(), $room->getNote());
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Erstellung des Raumes fehlgeschlagen");
        }

        return mysqli_insert_id($connection);
    }

    /**
     * @param Room $room
     * @throws \Exception
     */
    public static function updateRoom(Room $room)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("UPDATE raeume SET r_nr = ?, r_bezeichnung = ?, r_notiz = ? WHERE r_id = ?;");
        $query->bind_param("sssi", $room->getNumber(), $room->getDescription(), $room->getNote(), $room->getId());
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Ändern des Raumes fehlgeschlagen");
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public static function deleteRoomById($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("DELETE FROM raeume WHERE r_id = ?;");
        $query->bind_param("i", $id);
        $query->execute();

        $connection->query($query);

        if($connection->error)
        {
            throw new \Exception("Löschen des Raumes fehlgeschlagen");
        }
    }
}