<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 28.08.2017
 * Time: 10:48
 */

class Post {
    public static function createPost($postbody, $loggedInUserId, $profileUserId) {
        if (strlen($postbody) > 160 || strlen($postbody) < 1) {
            die('Nieprawidłowa długość postu!');
        }
        if ($loggedInUserId == $profileUserId) {
            DB::query('INSERT INTO posts (body,posted_at,user_id, likes) VALUES (:body, NOW(), :userid, 0)', array(':body'=>$postbody, ':userid'=>$profileUserId));
        } else {
            die('Nieprawidłowy użytkownik!');
        }
    }
    public static function likePost($postId, $likerId) {
        if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
            DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
            DB::query('INSERT INTO post_likes (post_id, user_id) VALUES (:postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
        } else {
            DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
            DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
        }
    }
    public static function displayPosts($userid, $username, $loggedInUserId) {
        $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
        $posts = "";
        foreach($dbposts as $p) {
            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {
                $posts .= htmlspecialchars($p['body'])."
                                <form action='profil.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='like' value='Lubie'>
                                        <span>".$p['likes']." polubień</span>
                                </form>
                                <hr /></br />
                                ";
            } else {
                $posts .= htmlspecialchars($p['body'])."
                                <form action='profil.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='unlike' value='Nie lubie'>
                                        <span>".$p['likes']." polubień</span>
                                </form>
                                <hr /></br />
                                ";
            }
        }
        return $posts;
    }
}
?>