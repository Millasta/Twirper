<?php
namespace Model\Hashtag;
use \Db;
use \PDOException;
/**
 * Hashtag model
 *
 * This file contains every db action regarding the hashtags
 */

/**
 * Attach a hashtag to a post
 * @param pid the post id to which attach the hashtag
 * @param hashtag_name the name of the hashtag to attach
 */
function attach($pid, $hashtag_name) {
    $db = \Db::dbc();

    // If it doesn't already exist in the DB HASHTAG
    $query = "SELECT COUNT(HASHTAGSTRING) FROM `HASHTAG` WHERE HASHTAGSTRING = '".$hashtag_name."' ";
    $result = $db->query($query);
    $result = $result->fetchAll();
    if($result[0][0] == 0) {
        $query = "INSERT INTO HASHTAG VALUES('".$hashtag_name."')";
        $result = $db->query($query);
        if($result == false)
            echo "\n Echec de l'opération : ajout du hashtag '".$hashtag_name."'\n";
    }

    $query = "INSERT INTO _LINK VALUES(".$pid.",'".$hashtag_name."')";
    $result = $db->query($query);
    if($result == FALSE)
        echo "Echec de l'opération LINK pour post ".$pid." et hashtag ".$hashtag_name." \n";
}

/**
 * List hashtags
 * @return a list of hashtags names
 */
function list_hashtags() {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT DISTINCT HASHTAGSTRING FROM HASHTAG WHERE 1";
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = $i[0];
    }
    return $list;
}

/**
 * @author Valentin MAURICE
 * List hashtags of a post
 * @param pid the post id
 * @return a list of hashtags names
 */
function list_post_hashtags($pid) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT DISTINCT HASHTAGSTRING FROM _LINK WHERE TWEETID = ".$pid;
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = $i[0];
    }
    return $list;
}

/**
 * List hashtags sorted per popularity (number of posts using each)
 * @param length number of hashtags to get at most
 * @return a list of hashtags
 */
function list_popular_hashtags($length) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT HASHTAGSTRING, COUNT(HASHTAGSTRING) as countH FROM _LINK GROUP BY HASHTAGSTRING ORDER BY countH DESC";
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            if(count($list) < $length)
                $list[] = $i[0];
    }
    return $list;
}

/**
 * Get posts for a hashtag
 * @param hashtag the hashtag name
 * @return a list of posts objects or null if the hashtag doesn't exist
 */
function get_posts($hashtag_name) {
    $list = array();
    $db = \Db::dbc();
    $query = "SELECT TWEETID FROM _LINK WHERE HASHTAGSTRING = '".$hashtag_name."'";
    $result = $db->query($query);
    $result = $result->fetchAll();
    $rowCount = count($result);
    if($result != FALSE && $rowCount > 0) {
        foreach($result as $i)
            $list[] = \Model\Post\get($i[0]);
    }
    else if ($rowcount == 0)
        return null;
    return $list;
}

/** Get related hashtags
 * @param hashtag_name the hashtag name
 * @param length the size of the returned list at most
 * @return an array of hashtags names
 */
function get_related_hashtags($hashtag_name, $length) {
    $posts = get_posts($hashtag_name);
    $list = array();
    $db = \Db::dbc();
    foreach($posts as $post) {
        // Get all the related hashtags
        $query = "SELECT HASHTAGSTRING FROM _LINK WHERE TWEETID = ".$post->id;
        $result = $db->query($query);
        $result = $result->fetchAll();
        foreach($result as $related_hashtag) {
            // Adding the current related hashtag to the list if it isn't already in it, and if the list is not too big
            if($related_hashtag[0] != $hashtag_name && count($list) < $length) {
                    $already_added = false;
                    foreach($list as $i) {
                        if($i == $related_hashtag[0])
                            $already_added = true;
                    }
                    if(!$already_added)
                        $list[] = $related_hashtag[0];
            }
        }
    }
    return $list;
}
