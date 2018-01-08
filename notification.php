<?php
namespace Model\Notification;
use \Db;
use \PDOException;
/**
 * Notification model
 *
 * This file contains every db action regarding the notifications
 */

/**
 * Get a liked notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the liked_by attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the reading_date attribute is either a DateTime object or null (if it hasn't been read)
 */
function get_liked_notifications($uid) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT * FROM _LIKE WHERE 1";
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i) {
            $post = \Model\Post\get($i["TWEETID"]);
            if($post->author->id == $uid) {
                $readdate = null;
                if($i["READDATE"] != null)
                    $readdate = new \DateTime($i["READDATE"]);
                $list[] = (object) array(
                    "type" => "liked",
                    "post" => \Model\Post\get($i["TWEETID"]),
                    "liked_by" => \Model\User\get($i["USERID"]),
                    "date" => new \DateTime($i["LIKEDATE"]),
                    "reading_date" => $readdate
                );
            }
        }
    }
    return $list;
}

/**
 * Mark a like notification as read (with date of reading)
 * @param pid the post id that has been liked
 * @param uid the user id that has liked the post
 */
function liked_notification_seen($pid, $uid) {
    $list = array();
    date_default_timezone_set("Europe/Paris");
    $db = \Db::dbc();
    $query = "UPDATE _LIKE SET READDATE = '".date("Y-m-d G:i:s")."' WHERE USERID = ".$uid." AND TWEETID = ".$pid;
    $result = $db->query($query);
    if($result == false)
        echo "Echec de l'opération UPDATE _LIKE READDATE pour post ".$pid." et utilisateur ".$uid."\n";
}

/**
 * Get a mentioned notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the mentioned_by attribute is a user object
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_mentioned_notifications($uid) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT * FROM _MENTION WHERE USERID = ".$uid;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i) {
            $post = \Model\Post\get($i["TWEETID"]);
            if($post != null){
                $readdate = null;
                if($i["READDATE"] != null)
                    $readdate = new \DateTime($i["READDATE"]);
                $list[] = (object) array(
                    "type" => "mentioned",
                    "post" => \Model\Post\get($i["TWEETID"]),
                    "mentioned_by" => \Model\User\get($post->author->id),
                    "date" => new \DateTime($post->date),
                    "reading_date" => $readdate
                );
            }
        }
    }
    return $list;
}

/**
 * Mark a mentioned notification as read (with date of reading)
 * @param uid the user that has been mentioned
 * @param pid the post where the user was mentioned
 */
function mentioned_notification_seen($uid, $pid) {
    $list = array();
    date_default_timezone_set("Europe/Paris");
    $db = \Db::dbc();
    $query = "UPDATE _MENTION SET READDATE = '".date("Y-m-d G:i:s")."' WHERE USERID = ".$uid." AND TWEETID = ".$pid;
    $result = $db->query($query);
    if($result == false)
        echo "Echec de l'opération UPDATE _MENTION READDATE pour post ".$pid." et utilisateur ".$uid."\n";
}

/**
 * Get a followed notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the user attribute is a user object which corresponds to the user following.
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_followed_notifications($uid) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT USERID_1, FOLLOWDATE, READDATE FROM _FOLLOW WHERE USERID_2 = ".$uid;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i) {
            $readdate = null;
            if($i["READDATE"] != null)
                $readdate = new \DateTime($i["READDATE"]);
            $list[] = (object) array(
                "type" => "followed",
                "user" => \Model\User\get($i["USERID_1"]),
                "date" => new \DateTime($i["FOLLOWDATE"]),
                "reading_date" => $readdate
            );
        }
    }
    return $list;
}

/**
 * Mark a followed notification as read (with date of reading)
 * @param followed_id the user id which has been followed
 * @param follower_id the user id that is following
 */
function followed_notification_seen($followed_id, $follower_id) {
    $list = array();
    date_default_timezone_set("Europe/Paris");
    $db = \Db::dbc();
    $query = "UPDATE _FOLLOW SET READDATE = '".date("Y-m-d G:i:s")."' WHERE USERID_1 = ".$follower_id." AND USERID_2 = ".$followed_id;
    $result = $db->query($query);
    if($result == false)
        echo "Echec de l'opération UPDATE _FOLLOW READDATE pour follower ".$follower_id." et followed ".$followed_id."\n";
}
