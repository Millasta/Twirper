<?php
namespace Model\Post;
use \Db;
use \PDOException;
/**
 * Post
 *
 * This file contains every db action regarding the posts
 */

/**
 * Get a post in db
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 */
function get($id) {
    if($id == null)
        return null;

    $db = \Db::dbc();
    $query = 'SELECT * FROM TWEET WHERE TWEETID = '.$id;
    $result = $db->query($query);

    if($result != FALSE && $result->rowCount() > 0) {
        $result = $result->fetchAll()[0];
        return (object) array(
            "id" => $result["TWEETID"],
            "text" => $result["TWEETCONTENT"],
            "date" => $result["TWEETPUBLICATIONDATE"],
            "author" => \Model\User\get($result["USERID"]),
        );
    }
    return NULL;
}

/**
 * Get a post with its likes, responses, the hashtags used and the post it was the response of
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the likes attribute is an array of users objects
 * @warning the hashtags attribute is an of hashtags objects
 * @warning the responds_to attribute is either null (if the post is not a response) or a post object
 */
function get_with_joins($id) {
    $db = \Db::dbc();
    $query = 'SELECT * FROM TWEET WHERE TWEETID = '.$id;
    $result = $db->query($query);

    if($result != FALSE && $result->rowCount() > 0) {
        $result = $result->fetchAll()[0];
        return (object) array(
            "id" => $result["TWEETID"],
            "text" => $result["TWEETCONTENT"],
            "date" => $result["TWEETPUBLICATIONDATE"],
            "author" => \Model\User\get($result["USERID"]),
            "likes" => get_likes($result["TWEETID"]),
            "hashtags" => \Model\Hashtag\list_post_hashtags($result["TWEETID"]),
            "responds_to" => get($result["TWEETISRESPONSETO"])
        );
    }
    return FALSE;
}

/**
 * Create a post in db
 * @param author_id the author user's id
 * @param text the message
 * @param mentioned_authors the array of ids of users who are mentioned in the post
 * @param response_to the id of the post which the creating post responds to
 * @return the id which was assigned to the created post, null if anything got wrong
 * @warning this function computes the date
 * @warning this function adds the mentions (after checking the users' existence)
 * @warning this function adds the hashtags
 * @warning this function takes care to rollback if one of the queries comes to fail.
 */
function create($author_id, $text, $response_to = null) {

    // A FAIRE : Ajouter les mentions
    $db = \Db::dbc();
    date_default_timezone_set("Europe/Paris");
    if($response_to == null)
        $response_to = "null";
    $query = "INSERT INTO TWEET VALUES(NULL,".$author_id.",'".date("Y-m-d G:i:s")."',".$response_to.",'".$text."')";
    $result = $db->query($query);
    if($result != FALSE) {
        $post_id = $db->lastInsertId();

        // Adding the mentions
        $mentioned_authors = \Model\Post\extract_mentions($text);
        if($mentioned_authors != null) {
            foreach($mentioned_authors as $mentioned_author) {
                $user = \Model\User\get_by_username($mentioned_author);
                // Test the existence of the user
                if($user != null) {

                    // Test if the user is already mentionned in this post
                    $users_mentioned = get_mentioned($post_id);
                    $already_mentioned = false;
                    foreach($users_mentioned as $user_mentioned) {
                        if($user_mentioned == $user) {
                            $already_mentioned = true;
                            $j = 9999;
                        }
                    }
                    if(!$already_mentioned);
                        mention_user($post_id, $user->id);
                }
            }
        }

        // Adding the HASHTAGS
        $hashtags = \Model\Post\extract_hashtags($text);
        $hashtags_count = count($hashtags);
        $hashtags_added = array();
        $already_added = false;
        foreach($hashtags as $hashtag) {
            // Test if the hashtag is already linked to this post
            foreach($hashtags_added as $hashtag_added) {
                if($hashtag_added == $hashtag)
                    $already_added = true;
            }

            // Adding it
            if(!$already_added) {
                \Model\Hashtag\attach($post_id, $hashtag);
                $hashtags_added[] = $hashtag;
                $already_added = false;
            }
        }

        return $post_id;
    }
    return NULL;
}

/**
 * Mention a user in a post
 * @param pid the post id
 * @param uid the user id to mention
 */
function mention_user($pid, $uid) {
    $db = \Db::dbc();
    $query = "INSERT INTO _MENTION VALUES(".$uid.",".$pid.",null)";
    $result = $db->query($query);
    if($result == FALSE)
        echo "Echec de l'opération MENTION pour utilisateur ".$uid." et post ".$pid." \n";
}

/**
 * Get mentioned user in post
 * @param pid the post id
 * @return the array of user objects mentioned
 */
function get_mentioned($pid) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT DISTINCT USERID FROM _MENTION WHERE TWEETID = ".$pid;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = \Model\User\get($i[0]);
    }
    return $list;
}

/**
 * Delete a post in db
 * @param id the id of the post to delete
 */
function destroy($id) {
    $db = \Db::dbc();
    $query = "DELETE FROM TWEET WHERE TWEETID = ".$id;
    $result = $db->query($query);
    if($result != FALSE)
        return TRUE;
    return FALSE;
}

/**
 * Search for posts
 * @param string the string to search in the text
 * @return an array of find objects
 */
function search($string) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT DISTINCT TWEETID FROM TWEET WHERE (TWEETCONTENT LIKE '%".$string."%')";
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = get($i[0]);
    }
    return $list;
}

/**
 * List posts
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return an array of the objects of each post
 */
function list_all($date_sorted=false) {
    $list = array();
    $db = \Db::dbc();
    if($date_sorted == false)
        $date_sorted = "";
    $query = "SELECT TWEETID FROM TWEET ORDER BY TWEETPUBLICATIONDATE ".$date_sorted;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = get($i[0]);
    }
    return $list;
}

/**
 * Get a user's posts
 * @param id the user's id
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return the list of posts objects
 */
function list_user_posts($id, $date_sorted="DESC") {
    $list = array();
    $db = \Db::dbc();
    if($date_sorted == false)
        $date_sorted = "";
    $query = "SELECT TWEETID FROM TWEET WHERE USERID = ".$id." ORDER BY TWEETPUBLICATIONDATE ".$date_sorted;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = get($i[0]);
    }
    return $list;
}

/**
 * Get a post's likes
 * @param pid the post's id
 * @return the users objects who liked the post
 */
function get_likes($pid) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT DISTINCT USERID FROM _LIKE WHERE TWEETID = ".$pid;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = \Model\User\get($i[0]);
    }
    return $list;
}

/**
 * Get a post's responses
 * @param pid the post's id
 * @return the posts objects which are a response to the actual post
 */
function get_responses($pid) {
    $list = array();
    $rowcount;
    $id = $pid;
    $db = \Db::dbc();
    $query = "SELECT TWEETID FROM TWEET WHERE TWEETISRESPONSETO = ".$id;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $response)
            $list[] = get($response[0]);
    }
    return $list;
}

/**
 * Get stats from a post (number of responses and number of likes
 */
function get_stats($pid) {
    $db = \Db::dbc();
    $query = "SELECT COUNT(USERID) FROM _LIKE WHERE TWEETID = ".$pid;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $nb_likes = $result[0][0];

    $post_id = $pid;
    $query = "SELECT TWEETID FROM TWEET WHERE TWEETISRESPONSETO = ".$post_id;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $nb_responses = count($result);

    return (object) array(
        "nb_likes" => $nb_likes,
        "nb_responses" => $nb_responses
    );

    /*return (object) array(
        "nb_likes" => 123,
        "nb_responses" => 123
    );*/
}

/**
 * Like a post
 * @param uid the user's id to like the post
 * @param pid the post's id to be liked
 */
function like($uid, $pid) {
    $db = \Db::dbc();
    date_default_timezone_set("Europe/Paris");
    $query = "INSERT INTO _LIKE VALUES(".$uid.",".$pid.",'".date("Y-m-d G:i:s")."',null)";
    $result = $db->query($query);
    if($result == FALSE)
        echo "Echec de l'opération LIKE pour utilisateur ".$uid." et post ".$pid." \n";
}

/**
 * Unlike a post
 * @param uid the user's id to unlike the post
 * @param pid the post's id to be unliked
 */
function unlike($uid, $pid) {
    $db = \Db::dbc();
    $query = "DELETE FROM _LIKE WHERE TWEETID = ".$pid." AND USERID = '".$uid."'";
    $result = $db->query($query);
    if($result == FALSE)
        echo "Echec de l'opération UNLIKE pour utilisateur ".$uid." et post ".$pid." \n";
}
