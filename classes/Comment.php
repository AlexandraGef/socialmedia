<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 28.08.2017
 * Time: 12:14
 */

class Comment {
    public static function createComment($commentBody, $postId, $userId) {
        if (strlen($commentBody) > 160 || strlen($commentBody) < 1) {
            die('Nieprawidłowa długośc!');
        }
        if (!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
            echo 'Nieprawidłowy post';
        } else {
            DB::query('INSERT INTO comments (comment, user_id, posted_at, post_id) VALUES (:comment, :userid, NOW(), :postid)', array(':comment'=>$commentBody, ':userid'=>$userId, ':postid'=>$postId));
        }
    }
    public static function displayComments($postId) {
        $comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid'=>$postId));
        foreach($comments as $comment) {
            echo $comment['comment']." ~ ".$comment['username']."<hr />";
        }
    }
}
?>