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
    $query = "SELECT * FROM USER WHERE USERID = " . $id;
    
	try {
	$req = $db->query($query);

		if ($req->rowCount() > 0) {
		    $user = $req->fetchAll()[0];

		    return (object) array(
		        "id" => $user["USERID"],
		        "username" => $user["USERUSERNAME"],
		        "name" => $user["USERDISPLAYNAME"],
		        "password" => $user["USERPASSWORD"],
		        "email" => $user["USEREMAILADDRESS"],
		        "avatar" => $user["USERAVATAR"]
		    );
		}
    }
	catch (PDOException $e) {
		print $e->getMessage();
	}

    return null;
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
 * @warning this function hashes the passhttps://www.google.fr/word
 */
function create($username, $name, $password, $email, $avatar_path) {
    $db = \Db::dbc();
    $query = "INSERT INTO USER VALUES(:USERID, :USERUSERNAME, :USERDISPLAYNAME, :USERSUBSCRIBINGDATE, :USEREMAILADDRESS, :USERPASSWORD, :USERAVATAR)";

	try {
		date_default_timezone_set("Europe/Paris");
		$req = $db->prepare($query);
		$req->execute(array(
			"USERID" => null,
			"USERUSERNAME" => $username,
			"USERDISPLAYNAME" => $name,
			"USERSUBSCRIBINGDATE" => date("Y-m-d G:i:s"),
			"USEREMAILADDRESS" => $email,
			"USERPASSWORD" => hash_password($password),
			"USERAVATAR" => $avatar_path
		));
		return $db->lastInsertId();
	}
	catch (PDOException $e) {
		print $e->getMessage();
		return null;
	}
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
    $query = "UPDATE USER SET USERUSERNAME = :USERUSERNAME, USERDISPLAYNAME = :USERDISPLAYNAME, USEREMAILADDRESS = :USEREMAILADDRESS WHERE USERID = " . $uid;
	
	try {
		$req = $db->prepare($query);
		$req->execute(array(
			"USERUSERNAME" => $username,
			"USERDISPLAYNAME" => $name,
			"USEREMAILADDRESS" => $email
		));
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param new_password the new password
 * @warning this function hashes the password
 */
function change_password($uid, $new_password) {
    $db = \Db::dbc();
    $query = "UPDATE USER SET USERPASSWORD = :USERPASSWORD WHERE USERID = " . $uid;
	
	try {
		$req = $db->prepare($query);
		$req->execute(array(
			"USERPASSWORD" => hash_password($new_password)
		));
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param avatar_path the temporary path to the user's avatar
 */
function change_avatar($uid, $avatar_path) {
    $db = \Db::dbc();
	$query = "UPDATE USER SET USERAVATAR = :USERAVATAR WHERE USERID = " . $uid;
	
	try {
		$req = $db->prepare($query);
		$req->execute(array(
			"USERAVATAR" => $avatar_path
		));
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}
}

/**
 * Delete a user in db
 * @param id the id of the user to delete
 * @return true if the user has been correctly deleted, false else
 */
function destroy($id) {
    $db = \Db::dbc(); 
	$query = "DELETE FROM USER WHERE USERID = " . $id;

	try {
		$req = $db->exec($query);
		return true;
	}
	catch (PDOException $e) {
		print $e->getMessage();
		return false;
	}
}

/**
 * Hash a user password
 * @param password the clear password to hash
 * @return the hashed password
 */
function hash_password($password) {
    return hash("md5", $password, false);
}

/**
 * Search a user
 * @param string the string to search in the name or username
 * @return an array of find objects
 */
function search($string) {
    $db = \Db::dbc();
    $users = array();
    $query = "SELECT DISTINCT USERID FROM USER WHERE (USERUSERNAME LIKE :SEARCH) OR (USERDISPLAYNAME LIKE :SEARCH)";

	try {
		$req = $db->prepare($query);
		$req->execute(array(
			"SEARCH" => "%" . $string . "%"
		));

		$data = $req->fetchAll();

        foreach ($data as $userID) {
            $users[] = get($userID[0]);
		}
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}

	return $users;
}

/**
 * List users
 * @return an array of the objects of every users
 */
function list_all() {
    $db = \Db::dbc();
    $users = array();
    $query = "SELECT DISTINCT USERID FROM USER";
    
	try {
		$req = $db->query($query);
		$data = $req->fetchAll();

		foreach($data as $user) {
		    $users[] = get($user[0]);
		}
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}

    return $users;
}

/**
 * Get a user from its username
 * @param username the searched user's username
 * @return the user object or null if the user doesn't exist
 */
function get_by_username($username) {
    $db = \Db::dbc();
    $query = "SELECT USERID FROM USER WHERE USERUSERNAME = '" . $username . "'";

	try {
		$req = $db->query($query);

		if ($req->rowCount() > 0) {
			$user = $req->fetchAll()[0];

			return get($user["USERID"]); 
		}
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}

	return null;
}

/**
 * Get a user's followers
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followers($uid) {
    $db = \Db::dbc();
    $users = array();
    $query = "SELECT DISTINCT USERID_1 AS USERID FROM _FOLLOW WHERE USERID_2 = " . $uid;
    
	try {
		$req = $db->query($query);

		if ($req->rowCount() > 0) {
			$data = $req->fetchAll();
		    
			foreach ($data as $userID) {
		        $users[] = get($userID["USERID"]);
			}
		}		
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}

	return $users;
}

/**
 * Get the users our user is following
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followings($uid) {
    $db = \Db::dbc();
    $users = array();
    $query = "SELECT DISTINCT USERID_2 AS USERID FROM _FOLLOW WHERE USERID_1 = " . $uid;

	try {
		$req = $db->query($query);

		if ($req->rowCount() > 0) {
			$data = $req->fetchAll();
		    
			foreach ($data as $userID) {
		        $users[] = get($userID["USERID"]);
			}
		}		
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}

	return $users;
}

/**
 * Get a user's stats
 * @param uid the user's id
 * @return an object which describes the stats
 */
function get_stats($uid) {
    $db = \Db::dbc();
    $list = array();
    $query_posts = "SELECT COUNT(USERID) FROM TWEET WHERE USERID = " . $uid;
    $query_followers = "SELECT COUNT(USERID_2) FROM _FOLLOW WHERE USERID_1 = " . $uid;
    $quer_following = "SELECT COUNT(USERID_1) FROM _FOLLOW WHERE USERID_2 = " . $uid;

	try {
		$req = $db->query($query);
		$nb_posts = $req->fetchAll()[0][0];

		$req = $db->query($query);
		$nb_followers = $req->fetchAll()[0][0];

		$req = $db->query($query);
		$nb_following = $req->fetchAll()[0][0];

		return (object) array(
		    "nb_posts" => $nb_posts,
		    "nb_followers" => $nb_followers,
		    "nb_following" => $nb_following
		);
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}

	return (object) null;
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
    $query = "SELECT USERID, USERPASSWORD FROM USER WHERE USERUSERNAME = :USERUSERNAME";

	try {
		$req = $db->prepare($query);
		$req->execute(array(
			"USERUSERNAME" => $username
		));

		if ($req->rowCount() > 0) {
			$user = $req->fetchAll()[0];
			
			if (hash_password($password) == $user["USERPASSWORD"]) {
				return get($user["USERID"]);
			}
		}
    }
	catch (PDOException $e) {
		print $e->getMessage();
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
    $query = "SELECT USERPASSWORD FROM USER WHERE USERID = :USERID";

	try {
		$req = $db->prepare($query);
		$req->execute(array(
			"USERID" => $id
		));

		if ($req->rowCount() > 0) {
			$user = $req->fetchAll()[0];
			
			if ($password == $user["USERPASSWORD"]) {
				return get($id);
			}
		}
    }
	catch (PDOException $e) {
		print $e->getMessage();
	}

	return null;
}

/**
 * Follow another user
 * @param id the current user's id
 * @param id_to_follow the user's id to follow
 */
function follow($id, $id_to_follow) {
    $db = \Db::dbc();
    $query = "INSERT INTO _FOLLOW VALUES(:USERID_1, :USERID_2, :FOLLOWDATE, :READDATE)";

	try {
		date_default_timezone_set("Europe/Paris");
		$req = $db->prepare($query);
		$req->execute(array(
			"USERID_1" => $id,
			"USERID_2" => $id_to_follow,
			"FOLLOWDATE" => date("Y-m-d G:i:s"),
			"READDATE" => null
		));
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}
}

/**
 * Unfollow a user
 * @param id the current user's id
 * @param id_to_follow the user's id to unfollow
 */
function unfollow($id, $id_to_unfollow) {
    $db = \Db::dbc();
    $query = "DELETE FROM _FOLLOW WHERE USERID_1 = " . $id . " AND USERID_2 = " . $id_to_unfollow;

	try {
		$req = $db->exec($query);
		return true;
	}
	catch (PDOException $e) {
		print $e->getMessage();
		return false;
	}
}
