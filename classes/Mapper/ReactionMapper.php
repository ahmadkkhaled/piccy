<?php
namespace Mapper;

use Model\Picture;
use \Model\Reaction;
use PDOStatement;
use Mapper\DatabaseConnection as DB;
use Model\REACTION_TYPE;

/**
 * Maps a Reaction object to a database row
 * This acts as storage manager for the Reaction entity
 */
class ReactionMapper{
    private static function bindReactionParameters(Reaction &$reaction, PDOStatement &$stmt){
        // Getters functions return value
        // We are extracting variables from getters because the bindParam() second argument is passed by reference
        $id = $reaction->getReactionID();
        $stmt->bindParam(':reactionid', $id);
        $userid = $reaction->getUserID();
        $stmt->bindParam(':userid', $userid);
        $pictureid = $reaction->getPictureID();
        $stmt->bindParam(':pictureid', $pictureid);
        $reactionType = $reaction->getType();
        $stmt->bindParam(':reactionType', $reactionType); // using reactionType because type is reserved keyword
        $createdat = $reaction->getCreatedAt();
        $stmt->bindParam(':createdat', $createdat);
    }
    static function add(Reaction $reaction){
        $stmt = DB::prepare('INSERT INTO `Reaction` (`ReactionID`, `UserID`, `PictureID`, `Type`, `CreatedAt`) VALUES (:reactionid, :userid, :pictureid, :reactionType, :createdat)');
        ReactionMapper::bindReactionParameters($reaction, $stmt);
        $stmt->execute();
    }

    static function getReactionsByPicture(Picture $picture){
        $pictureid = $picture->getPictureID();
        $stmt = DB::prepare('SELECT * FROM Reaction WHERE PictureID = :pictureid');
        $stmt->bindParam(':pictureid', $pictureid);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $reactions = [];
        if($stmt->rowCount() > 0){
            while($row = $stmt->fetch()){
                $reaction = new Reaction($row['ReactionID'], $row['UserID'], $row['PictureID'], $row['Type'], $row['CreatedAt']);
                $reactions[] = $reaction;
            }
        }
        return $reactions;
    }
    static function update(Reaction $reaction){
        $stmt = DB::prepare('UPDATE `Reaction` SET `UserID` = :userid, `PictureID` = :pictureid,
            `Type` = :reactionType, `CreatedAt` = :createdat WHERE `ReactionID` = :reactionid');
        ReactionMapper::bindReactionParameters($reaction, $stmt);
        $stmt->execute();
    }
    static function delete(Reaction $reaction){
        $stmt = DB::prepare('DELETE FROM `Reaction` WHERE `ReactionID` = :reactionid');
        $id = $reaction->getReactionId();
        $stmt->bindParam(':reactionid', $id);
        $stmt->execute();
    }

    static function getNumberOfReactsTypeByPicture($picture, $type)
    {
        $reactions = ReactionMapper::getReactionsByPicture($picture);
        return ReactionMapper::getNumberOfReactsType($reactions, $type);
    }

    static function getNumberOfReactsType($reactions, $type)
    {
        $count = 0;
        foreach ($reactions as $reaction) {
            if ($reaction->getType() === $type)
                $count++;
        }
        return $count;
    }

    static function getReactionByUserAndPicture($userid, $pictureid){
        $stmt = DB::prepare('SELECT * FROM Reaction WHERE PictureID = :pictureid AND UserID = :userid');
        $stmt->bindParam(':pictureid', $pictureid);
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $reaction = null;
        if($stmt->rowCount() > 0){
            $row = $stmt->fetch();
            $reaction = new Reaction($row['ReactionID'], $row['UserID'], $row['PictureID'], $row['Type'], $row['CreatedAt']);
        }
        return $reaction;
    }
}
