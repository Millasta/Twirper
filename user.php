<?php
namespace Model\User;
use \Db;
use \PDOException;
/**
 * User model
 *
 * This file contains every db action regarding the users
 */

/**
 * Get a user in db
 * @param id the id of the user in db
 * @return an object containing the attributes of the user or null if error or the user doesn't exist
 */
function get($id) {
    $db = \Db::dbc();
    $query = 'SELECT * FROM USER WHERE USERID = '.$id;
    $result = $db->query($query);

    if($result != FALSE && $result->rowCount() > 0) {
        $result = $result->fetchAll()[0];
        return (object) array(
            "id" => $result["USERID"],
            "username" => $result["USERUSERNAME"],
            "name" => $result["USERDISPLAYNAME"],
            "password" => $result["USERPASSWORD"],
            "email" => $result["USEREMAILADDRESS"],
            "avatar" => $result["USERAVATAR"]
        );
    }
    return NULL;
}

/**
 * Create a user in db
 * @param username the user's username
 * @param name the user's name
 * @param password the user's password
 * @param email the user's email
 * @param avatar_path the temporary path to the user's avatar
 * @return the id which was assigned to the created user, null if an error occured
 * @warning this function doesn't check whether a user with a similar username exists
 * @warning this function hashes the password
 */
function create($username, $name, $password, $email, $avatar_path) {
    $db = \Db::dbc();
    $query = 'INSERT INTO USER VALUES(NULL,"'.$username.'","'.$name.'","'.date("Y-m-d").'","'.$email.'","'.hash_password($password).'","'.$avatar_path.'")';
    $result = $db->query($query);
    if($result != FALSE) {
        echo "\nID added : ".$db->lastInsertId().", user : ".$username."\n";
        return $db->lastInsertId();
    }
    return NULL;
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param username the user's username
 * @param name the user's name
 * @param email the user's email
 * @warning this function doesn't check whether a user with a similar username exists
 */
function modify($uid, $username, $name, $email) {
    $db = \Db::dbc();
    $query = "UPDATE USER SET USERUSERNAME = '".$username."', USERDISPLAYNAME = '".$name."', USEREMAILADDRESS = '".$email."' WHERE USERID = ".$uid;
    $result = $db->query($query);
    if($result == FALSE)
        echo "Modify query failed.";
    else
        echo "\nUser ".$uid." modified.\n";
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param new_password the new password
 * @warning this function hashes the password
 */
function change_password($uid, $new_password) {
    $db = \Db::dbc();
    $query = "UPDATE USER SET USERPASSWORD = '".hash_password($new_password)."' WHERE USERID = ".$uid;
    $result = $db->query($query);
    if($result == FALSE)
        echo "Echec du changemet de password pour ID ".$uid;
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param avatar_path the temporary path to the user's avatar
 */
function change_avatar($uid, $avatar_path) {
    $db = \Db::dbc();
    $query = "UPDATE USER SET USERAVATAR = '".$avatar_path."' WHERE USERID = ".$uid;
    $result = $db->query($query);
    if($result == FALSE)
        echo "Echec du changemet d'avatar pour ID ".$uid;
}

/**
 * Delete a user in db
 * @param id the id of the user to delete
 * @return true if the user has been correctly deleted, false else
 */
function destroy($id) {
    $db = \Db::dbc();
    $query = "DELETE FROM USER WHERE USERID = ".$id;
    $result = $db->query($query);
    if($result != FALSE){
        echo "\nUser ".$id." deleted.\n";
        return TRUE;
    }
    return FALSE;
}

/**
 * Hash a user password
 * @param password the clear password to hash
 * @return the hashed password
 */
function hash_password($password) {
    $password = hash("md5", $password, FALSE);
    return $password;
}

/**
 * Search a user
 * @param string the string to search in the name or username
 * @return an array of find objects
 */
function search($string) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT DISTINCT USERID FROM USER WHERE (USERUSERNAME LIKE '%".$string."%') OR (USERDISPLAYNAME LIKE '%".$string."')";
    $result = $db->query($query);

    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        for($i = 0 ; $i < $rowCount ; $i++)
            $list[] = get($result[$i][0]);
    }
    return $list;
}

/**
 * List users
 * @return an array of the objects of every users
 */
function list_all() {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT USERID FROM USER";
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        for($i = 0 ; $i < $rowCount ; $i++)
            $list[] = get($result[$i][0]);
    }
    return $list;
}

/**
 * Get a user from its username
 * @param username the searched user's username
 * @return the user object or null if the user doesn't exist
 */
function get_by_username($username) {
    $db = \Db::dbc();
    $query = "SELECT USERID FROM USER WHERE USERUSERNAME = '".$username."'";
    $result = $db->query($query);
    if($result != FALSE && $result->rowCount() > 0) {
        $result = $result->fetchAll()[0];
        return get($result["USERID"]);
    }
    return null;
}

/**
 * Get a user's followers
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followers($uid) {
    return [get(2)];
}

/**
 * Get the users our user is following
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followings($uid) {
    return [get(2)];
}

/**
 * Get a user's stats
 * @param uid the user's id
 * @return an object which describes the stats
 */
function get_stats($uid) {
    return (object) array(
        "nb_posts" => 10,
        "nb_followers" => 50,
        "nb_following" => 66
    );
}

/**
 * Verify the user authentification
 * @param username the user's username
 * @param password the user's password
 * @return the user object or null if authentification failed
 * @warning this function must perform the password hashing
 */
function check_auth($username, $password) {
    $db = \Db::dbc();
    $query = "SELECT USERID, USERPASSWORD FROM USER WHERE USERUSERNAME = '".$username."'";
    $result = $db->query($query);
    if($result != FALSE && $result->rowCount() > 0) {
        $result = $result->fetchAll()[0];
        $userpassword = hash_password($password);
        if($userpassword == $result["USERPASSWORD"])
            return get($result["USERID"]);
    }
    return null;
}

/**
 * Verify the user authentification based on id
 * @param id the user's id
 * @param password the user's password (already hashed)
 * @return the user object or null if authentification failed
 */
function check_auth_id($id, $password) {
    $db = \Db::dbc();
    $query = "SELECT USERPASSWORD FROM USER WHERE USERID = ".$id;
    $result = $db->query($query);
    if($result != FALSE && $result->rowCount() > 0) {
        $result = $result->fetchAll()[0];
        if($password == $result["USERPASSWORD"])
            return get($id);
    }
    return null;
}

/**
 * Follow another user
 * @param id the current user's id
 * @param id_to_follow the user's id to follow
 */
function follow($id, $id_to_follow) {
}

/**
 * Unfollow a user
 * @param id the current user's id
 * @param id_to_follow the user's id to unfollow
 */
function unfollow($id, $id_to_unfollow) {
}
