<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 25.07.2017
 * Time: 09:21
 */

namespace AppBundle\Entity\Repository;

/**
 * Provides functions to modify room entities in
 * the database.
 */

use AppBundle\Entity\ManagedConnection;
use AppBundle\Entity\Room;
use Symfony\Component\VarDumper\VarDumper;

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

        if($result === false)
        {
            throw new \Exception("Selektieren der Räume fehlgeschlagen");
        }

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

        $roomId = 0;

        $query->bind_param("i", $roomId);

        $roomId = $id;

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren des Raumes fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();

        $row = $result->fetch_assoc();

        if($row == NULL)
        {
            return NULL;
        }

        $room = new Room();
        $room->setId($row["r_id"]);
        $room->setNumber($row["r_nr"]);
        $room->setDescription($row["r_bezeichnung"]);
        $room->setNote($row["r_notiz"]);

        return $room;
    }

    /**
     * @param $name
     * @return Room|null
     * @throws \Exception
     */
    public static function getRoomByNumber($name)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM raeume WHERE r_nr = ?;");

        $roomName = 0;

        $query->bind_param("i", $roomName);

        $roomName = $name;

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren des Raumes fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();

        $row = $result->fetch_assoc();

        if($row == NULL)
        {
            return NULL;
        }

        $room = new Room();
        $room->setId($row["r_id"]);
        $room->setNumber($row["r_nr"]);
        $room->setDescription($row["r_bezeichnung"]);
        $room->setNote($row["r_notiz"]);

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

        $roomId = 0;
        $roomNr = 0;
        $roomNote = 0;

        $query->bind_param("sss", $roomId , $roomNr, $roomNote);

        $roomId = $room->getNumber();
        $roomNr = $room->getDescription();
        $roomNote = $room->getNote();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Erstellung des Raumes fehlgeschlagen");
        }

        $query->close();

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

        $roomNr = 0;
        $roomDesc = 0;
        $roomNote = 0;
        $roomId = 0;

        $query->bind_param("sssi", $roomNr, $roomDesc, $roomNote, $roomId);

        $roomNr = $room->getNumber();
        $roomDesc = $room->getDescription();
        $roomNote = $room->getNote();
        $roomId = $room->getId();

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Ändern des Raumes fehlgeschlagen");
        }

        $query->close();
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public static function canRoomBeDeleted($id)
    {
        $managedConnection = new ManagedConnection();
        $connection = $managedConnection->getConnection();

        $query = $connection->prepare("SELECT * FROM raeume INNER JOIN komponenten AS komp ON raeume.r_id = komp.k_id WHERE raeume.r_id = ?;");

        $roomId = 0;

        $query->bind_param("i", $roomId);

        $roomId = $id;

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Selektieren des Raumes fehlgeschlagen");
        }

        $result = $query->get_result();
        $query->close();

        if($row = $result->fetch_assoc())
        {
            return false;
        }

        return true;
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

        $roomId = 0;

        $query->bind_param("i", $roomId);

        $roomId = $id;

        $query->execute();

        if($query->error)
        {
            $query->close();
            throw new \Exception("Löschen des Raumes fehlgeschlagen");
        }

        $query->close();
    }
}