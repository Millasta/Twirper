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
}

/**
 * List hashtags
 * @return a list of hashtags names
 */
function list_hashtags() {
    return ["Test"];
}

/**
 * List hashtags sorted per popularity (number of posts using each)
 * @param length number of hashtags to get at most
 * @return a list of hashtags
 */
function list_popular_hashtags($length) {
    return ["Hallo"];
}

/**
 * Get posts for a hashtag
 * @param hashtag the hashtag name
 * @return a list of posts objects or null if the hashtag doesn't exist
 */
function get_posts($hashtag_name) {
    return [\Model\Post\get(1)];
}

/** Get related hashtags
 * @param hashtag_name the hashtag name
 * @param length the size of the returned list at most
 * @return an array of hashtags names
 */
function get_related_hashtags($hashtag_name, $length) {
    return ["Hello"];
}
